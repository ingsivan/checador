<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * A Monday-to-Sunday week picked from the "semana" query parameter (any date inside it).
 */
class Week
{
    public function __construct(public readonly Carbon $start, public readonly Carbon $end) {}

    public static function fromRequest(Request $request): self
    {
        try {
            $date = Carbon::parse($request->query('semana', today()->toDateString()));
        } catch (\Throwable) {
            $date = today();
        }

        return new self($date->copy()->startOfWeek(), $date->copy()->endOfWeek());
    }

    public function previous(): string
    {
        return $this->start->copy()->subWeek()->toDateString();
    }

    public function next(): ?string
    {
        $next = $this->start->copy()->addWeek();

        return $next->isFuture() ? null : $next->toDateString();
    }

    public function label(): string
    {
        $sameMonth = $this->start->month === $this->end->month;

        return $this->start->translatedFormat($sameMonth ? 'j' : 'j M')
            .' – '.$this->end->translatedFormat('j M Y');
    }

    public function isCurrent(): bool
    {
        return $this->start->isSameDay(today()->startOfWeek());
    }
}
