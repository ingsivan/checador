<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

/*
 * Create the first real administrator on a fresh install (no demo data needed):
 *   php artisan checador:admin "Nereo" nereo@empresa.com
 */
Artisan::command('checador:admin {name} {email}', function (string $name, string $email) {
    $password = $this->secret('Contraseña (mínimo 8 caracteres)');

    $validator = Validator::make(compact('name', 'email', 'password'), [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8'],
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    User::create([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => User::ROLE_ADMIN,
        'position' => 'Administrador',
    ]);

    $this->info("Listo. Ya puedes entrar con {$email}.");
})->purpose('Crear un usuario administrador');
