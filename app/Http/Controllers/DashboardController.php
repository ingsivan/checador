<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $attendance = $user->todayAttendance()->first()
            ?? new Attendance(['user_id' => $user->id, 'date' => today()]);
        $attendance->setRelation('user', $user);

        $week = $user->attendances()
            ->whereBetween('date', [today()->startOfWeek(), today()->endOfWeek()])
            ->get();

        $team = User::active()->with('todayAttendance')->get()
            ->countBy(fn (User $member) => $member->todayAttendance?->status() ?? Attendance::STATUS_OUT);

        return view('dashboard', [
            'user' => $user,
            'attendance' => $attendance,
            'weekLunchMinutes' => $week->sum(fn (Attendance $day) => $day->lunchMinutes()),
            'weekWorkedMinutes' => $week->sum(fn (Attendance $day) => $day->workedMinutes()),
            'myTasks' => $user->tasks()->with('project')->where('status', 'doing')->orderBy('position')->get(),
            'team' => $team,
            'lunchLimit' => config('checador.lunch_minutes'),
        ]);
    }
}
