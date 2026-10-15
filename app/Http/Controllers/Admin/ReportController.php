<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Support\Week;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $week = Week::fromRequest($request);
        $rows = $this->rows($week);

        return view('admin.reports.index', [
            'week' => $week,
            'rows' => $rows,
            'ranking' => $rows->filter(fn ($r) => $r['days'] > 0)->sortByDesc('avg_lunch')->values(),
            'lunchLimit' => config('checador.lunch_minutes'),
            'totals' => [
                'lunch' => $rows->sum('lunch'),
                'late' => $rows->sum('late_days'),
                'over' => $rows->sum('over_days'),
                'worked' => $rows->sum('worked'),
            ],
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $week = Week::fromRequest($request);
        $rows = $this->rows($week);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel opens the accents correctly
            fputcsv($out, ['Empleado', 'Días', 'Horas trabajadas', 'Min. comida', 'Promedio comida', 'Días pasado de comida', 'Llegadas tarde', 'Días sin salida']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['user']->name, $r['days'], round($r['worked'] / 60, 1), $r['lunch'], $r['avg_lunch'], $r['over_days'], $r['late_days'], $r['open_days']]);
            }
            fclose($out);
        }, 'reporte-'.$week->start->toDateString().'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Weekly totals per active employee.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function rows(Week $week): Collection
    {
        $limit = config('checador.lunch_minutes');

        return User::active()
            ->with(['attendances' => fn ($q) => $q->whereBetween('date', [$week->start, $week->end])])
            ->orderBy('name')
            ->get()
            ->map(function (User $user) use ($limit) {
                $days = $user->attendances->each(fn (Attendance $d) => $d->setRelation('user', $user));
                $lunchDays = $days->filter(fn (Attendance $d) => $d->lunch_out_at !== null);
                $lunch = $lunchDays->sum(fn (Attendance $d) => $d->lunchMinutes());

                return [
                    'user' => $user,
                    'days' => $days->whereNotNull('check_in_at')->count(),
                    'worked' => $days->sum(fn (Attendance $d) => $d->workedMinutes()),
                    'lunch' => $lunch,
                    'avg_lunch' => $lunchDays->count() ? (int) round($lunch / $lunchDays->count()) : 0,
                    'max_lunch' => (int) $lunchDays->max(fn (Attendance $d) => $d->lunchMinutes()),
                    'over_days' => $lunchDays->filter(fn (Attendance $d) => $d->lunchMinutes() > $limit)->count(),
                    'late_days' => $days->filter(fn (Attendance $d) => $d->lateMinutes() > 0)->count(),
                    'late_minutes' => $days->sum(fn (Attendance $d) => $d->lateMinutes()),
                    'open_days' => $days->filter(fn (Attendance $d) => $d->check_in_at && ! $d->check_out_at && ! $d->date->isToday())->count(),
                ];
            });
    }
}
