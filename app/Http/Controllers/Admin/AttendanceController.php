<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Support\Week;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Lets the admin fix a person's marks (forgotten check-out, wrong tap).
 */
class AttendanceController extends Controller
{
    public function edit(Request $request, User $user): View
    {
        $week = Week::fromRequest($request);

        $records = $user->attendances()
            ->whereBetween('date', [$week->start, $week->end])
            ->get()
            ->keyBy(fn (Attendance $a) => $a->date->toDateString());

        $days = collect();
        for ($day = $week->start->copy(); $day->lte($week->end) && $day->lte(today()); $day->addDay()) {
            $record = $records->get($day->toDateString());
            if ($record || $day->isWeekday()) {
                $days->push(['date' => $day->copy(), 'record' => $record?->setRelation('user', $user)]);
            }
        }

        return view('admin.attendance.edit', [
            'employee' => $user,
            'week' => $week,
            'days' => $days->reverse()->values(),
        ]);
    }

    public function update(Request $request, User $user, string $date): RedirectResponse
    {
        $day = Carbon::parse($date)->startOfDay();
        abort_if($day->isFuture(), 422);

        $times = $request->validate(array_fill_keys(array_keys(Attendance::STEPS), ['nullable', 'date_format:H:i']));

        // Marks must be in order and lunch needs both ends.
        $previous = null;
        foreach (Attendance::STEPS as $field => $label) {
            if (filled($times[$field] ?? null)) {
                if ($previous && $times[$field] <= $previous) {
                    throw ValidationException::withMessages([$field => "La hora de {$label} debe ser después de la anterior."]);
                }
                $previous = $times[$field];
            }
        }
        if (filled($times['lunch_in_at'] ?? null) && blank($times['lunch_out_at'] ?? null)) {
            throw ValidationException::withMessages(['lunch_out_at' => 'Falta la hora de salida a comer.']);
        }
        if (blank($times['check_in_at'] ?? null) && collect($times)->filter()->isNotEmpty()) {
            throw ValidationException::withMessages(['check_in_at' => 'Falta la hora de llegada.']);
        }

        $attendance = $user->attendances()->whereDate('date', $day)->first();

        if (collect($times)->filter()->isEmpty()) {
            $attendance?->delete();
        } else {
            $attendance ??= new Attendance(['user_id' => $user->id, 'date' => $day]);
            foreach (array_keys(Attendance::STEPS) as $field) {
                $attendance->{$field} = filled($times[$field] ?? null) ? $day->copy()->setTimeFromTimeString($times[$field]) : null;
            }
            $attendance->save();
        }

        return back()->with('status', 'Se corrigió el '.$day->translatedFormat('l j').' de '.$user->name.'.');
    }
}
