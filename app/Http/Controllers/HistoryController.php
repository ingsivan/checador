<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Support\Week;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $week = Week::fromRequest($request);

        $days = $user->attendances()
            ->whereBetween('date', [$week->start, $week->end])
            ->orderByDesc('date')
            ->get()
            ->each(fn (Attendance $day) => $day->setRelation('user', $user));

        return view('history', [
            'week' => $week,
            'days' => $days,
            'totalLunch' => $days->sum(fn (Attendance $d) => $d->lunchMinutes()),
            'totalWorked' => $days->sum(fn (Attendance $d) => $d->workedMinutes()),
            'lateCount' => $days->filter(fn (Attendance $d) => $d->lateMinutes() > 0)->count(),
            'lunchLimit' => config('checador.lunch_minutes'),
        ]);
    }
}
