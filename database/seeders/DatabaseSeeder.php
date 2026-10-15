<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Test data: one admin and ten developers, all with the password "password".
     * Change these passwords before using the app with the real team.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Nereo Admin',
            'email' => 'admin@checador.test',
            'position' => 'Líder de equipo',
        ]);

        $developers = [
            ['Ana López', 'Frontend', '09:00'],
            ['Carlos Méndez', 'Backend', '09:00'],
            ['Daniela Ruiz', 'Full stack', '09:30'],
            ['Eduardo Torres', 'Backend', '08:30'],
            ['Fernanda Castillo', 'Diseño UX/UI', '09:00'],
            ['Gabriel Ortiz', 'QA', '10:00'],
            ['Héctor Ramírez', 'DevOps', '09:00'],
            ['Isabel Navarro', 'Frontend', '09:00'],
            ['Jorge Herrera', 'Móvil', '09:30'],
            ['Karla Jiménez', 'Full stack', '09:00'],
        ];

        foreach ($developers as [$name, $position, $start]) {
            $email = str($name)->ascii()->lower()->replace(' ', '.').'@checador.test';

            User::factory()->create([
                'name' => $name,
                'email' => $email,
                'position' => $position,
                'work_start' => $start,
                'work_end' => date('H:i', strtotime($start) + 9 * 3600),
            ]);
        }
    }
}
