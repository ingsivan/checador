<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Two weeks of fake check-ins, a few projects and kanban cards, so every screen
 * has something to show. Run only on test data.
 */
class DemoActivitySeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(42);

        $developers = User::where('role', User::ROLE_DEVELOPER)->orderBy('id')->get();

        // Some people are habitually late or take long lunches, so the ranking has a story.
        $lunchHabit = [45, 55, 75, 50, 60, 85, 40, 65, 70, 52];
        $lateHabit = [0, 5, 20, 0, 0, 25, 0, 10, 15, 0];

        $today = today();
        $day = $today->copy()->subWeeks(2)->startOfWeek();

        while ($day->lt($today)) {
            if ($day->isWeekday()) {
                foreach ($developers as $i => $dev) {
                    if (mt_rand(1, 100) <= 6) {
                        continue; // day off
                    }

                    $start = $day->copy()->setTimeFromTimeString($dev->work_start);
                    $checkIn = $start->copy()->addMinutes(mt_rand(-15, 8) + (mt_rand(1, 100) <= 40 ? $lateHabit[$i % 10] : 0));
                    $lunchOut = $day->copy()->setTime(14, 0)->addMinutes(mt_rand(-30, 45));
                    $lunchIn = $lunchOut->copy()->addMinutes(max(25, $lunchHabit[$i % 10] + mt_rand(-12, 15)));
                    $checkOut = $day->copy()->setTimeFromTimeString($dev->work_end)->addMinutes(mt_rand(-20, 40));

                    Attendance::create([
                        'user_id' => $dev->id,
                        'date' => $day->toDateString(),
                        'check_in_at' => $checkIn,
                        'lunch_out_at' => $lunchOut,
                        'lunch_in_at' => $lunchIn,
                        // Now and then someone forgets to check out.
                        'check_out_at' => mt_rand(1, 100) <= 4 ? null : $checkOut,
                    ]);
                }
            }
            $day->addDay();
        }

        $this->seedToday($developers);
        $this->seedBoard($developers);
    }

    /**
     * Today: a mix of people working, at lunch and not yet arrived, relative to "now".
     */
    private function seedToday($developers): void
    {
        $now = Carbon::now();

        foreach ($developers as $i => $dev) {
            $marks = ['user_id' => $dev->id, 'date' => $now->toDateString()];

            match ($i % 5) {
                0, 1 => $marks += ['check_in_at' => $now->copy()->subMinutes(180 + $i * 7)],
                2 => $marks += ['check_in_at' => $now->copy()->subMinutes(240), 'lunch_out_at' => $now->copy()->subMinutes(22 + $i)],
                3 => $marks += ['check_in_at' => $now->copy()->subMinutes(300), 'lunch_out_at' => $now->copy()->subMinutes(72)],
                default => null,
            };

            if (count($marks) > 2) {
                Attendance::create($marks);
            }
        }
    }

    private function seedBoard($developers): void
    {
        $projects = collect([
            ['Portal de clientes', 'Grupo Andes', 'indigo'],
            ['App de reservas', 'ReservaPro', 'emerald'],
            ['Tienda en línea', 'Moda MX', 'rose'],
            ['Interno: Checador', null, 'amber'],
        ])->map(fn ($p) => Project::create(['name' => $p[0], 'client' => $p[1], 'color' => $p[2], 'is_active' => true]));

        $cards = [
            ['Login con Google', 'doing'], ['Diseño del carrito', 'todo'], ['API de pagos', 'doing'],
            ['Pruebas de checkout', 'review'], ['Notificaciones push', 'todo'], ['Migrar a Laravel 13', 'done'],
            ['Pantalla de perfil', 'doing'], ['Reporte de ventas', 'todo'], ['Corregir bug en filtros', 'review'],
            ['Optimizar imágenes', 'done'], ['Calendario de citas', 'doing'], ['Onboarding móvil', 'todo'],
            ['Modo oscuro', 'done'], ['Exportar a Excel', 'doing'], ['Documentar API', 'todo'],
            ['Landing de campaña', 'review'], ['Recordatorios por correo', 'todo'], ['Dashboard de admin', 'doing'],
        ];

        foreach ($cards as $i => [$title, $status]) {
            Task::create([
                'project_id' => $projects[$i % $projects->count()]->id,
                'user_id' => $developers[$i % $developers->count()]->id,
                'title' => $title,
                'status' => $status,
                'position' => $i,
                'due_date' => $status === 'done' ? null : today()->addDays(($i % 9) + 1),
            ]);
        }
    }
}
