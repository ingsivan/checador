<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function __invoke(): View
    {
        $members = User::active()
            ->with(['todayAttendance', 'tasks' => fn ($q) => $q->where('status', 'doing')->with('project')])
            ->orderBy('name')
            ->get();

        foreach ($members as $member) {
            $member->todayAttendance?->setRelation('user', $member);
        }

        $order = [Attendance::STATUS_LUNCH => 0, Attendance::STATUS_WORKING => 1, Attendance::STATUS_DONE => 2, Attendance::STATUS_OUT => 3];

        $members = $members->sortBy(fn (User $m) => $order[$m->todayAttendance?->status() ?? Attendance::STATUS_OUT])->values();

        return view('team', [
            'members' => $members,
            'counts' => $members->countBy(fn (User $m) => $m->todayAttendance?->status() ?? Attendance::STATUS_OUT),
            'lunchLimit' => config('checador.lunch_minutes'),
        ]);
    }
}
