<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable(['user_id', 'date', 'check_in_at', 'lunch_out_at', 'lunch_in_at', 'check_out_at'])]
class Attendance extends Model
{
    public const STATUS_OUT = 'out';

    public const STATUS_WORKING = 'working';

    public const STATUS_LUNCH = 'lunch';

    public const STATUS_DONE = 'done';

    /**
     * The four marks of a day, in the order they must happen.
     *
     * @var array<string, string>
     */
    public const STEPS = [
        'check_in_at' => 'Llegada',
        'lunch_out_at' => 'Salida a comer',
        'lunch_in_at' => 'Regreso de comer',
        'check_out_at' => 'Salida',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_at' => 'datetime',
            'lunch_out_at' => 'datetime',
            'lunch_in_at' => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): string
    {
        return match (true) {
            $this->check_out_at !== null => self::STATUS_DONE,
            $this->lunch_out_at !== null && $this->lunch_in_at === null => self::STATUS_LUNCH,
            $this->check_in_at !== null => self::STATUS_WORKING,
            default => self::STATUS_OUT,
        };
    }

    /**
     * Marks the employee can register right now. Lunch is optional: after arriving
     * they can either go to lunch or leave for the day.
     *
     * @return list<string>
     */
    public function allowedSteps(): array
    {
        return match (true) {
            $this->check_in_at === null => ['check_in_at'],
            $this->check_out_at !== null => [],
            $this->lunch_out_at === null => ['lunch_out_at', 'check_out_at'],
            $this->lunch_in_at === null => ['lunch_in_at'],
            default => ['check_out_at'],
        };
    }

    /**
     * Seconds worked so far, excluding lunch.
     */
    public function workedSeconds(?CarbonInterface $now = null): int
    {
        if ($this->check_in_at === null) {
            return 0;
        }

        if ($this->check_out_at === null && ! $this->isOpenToday()) {
            return 0; // forgot to check out: flagged as "sin salida", not counted
        }

        $now ??= Carbon::now();
        $end = $this->check_out_at ?? $now;
        $lunch = 0;

        if ($this->lunch_out_at !== null) {
            $lunch = (int) $this->lunch_out_at->diffInSeconds($this->lunch_in_at ?? $end);
        }

        return max(0, (int) $this->check_in_at->diffInSeconds($end) - $lunch);
    }

    /**
     * Minutes spent at lunch. While still at lunch it counts up to "now".
     */
    public function lunchMinutes(?CarbonInterface $now = null): int
    {
        if ($this->lunch_out_at === null) {
            return 0;
        }

        $end = $this->lunch_in_at ?? $this->check_out_at;

        if ($end === null) {
            if (! $this->isOpenToday()) {
                return 0; // a past day nobody closed: we can't know how long it was
            }
            $end = $now ?? Carbon::now();
        }

        return (int) $this->lunch_out_at->diffInMinutes($end);
    }

    /**
     * Minutes worked, excluding lunch. Open days count up to "now".
     */
    public function workedMinutes(?CarbonInterface $now = null): int
    {
        return intdiv($this->workedSeconds($now), 60);
    }

    /**
     * Only today's record can still be running; older ones without an end are incomplete.
     */
    public function isOpenToday(): bool
    {
        return $this->date === null || $this->date->isToday();
    }

    public function isOverLunch(?CarbonInterface $now = null): bool
    {
        return $this->lunchMinutes($now) > config('checador.lunch_minutes');
    }

    /**
     * Minutes after the scheduled start (beyond tolerance). Zero when on time.
     */
    public function lateMinutes(): int
    {
        if ($this->check_in_at === null) {
            return 0;
        }

        $scheduled = $this->date->copy()->setTimeFromTimeString($this->user->work_start)
            ->addMinutes(config('checador.late_tolerance_minutes'));

        return $this->check_in_at->gt($scheduled)
            ? (int) $scheduled->diffInMinutes($this->check_in_at)
            : 0;
    }
}
