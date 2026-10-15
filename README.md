# Checador

Sistema para registrar llegadas, salidas a comer, regresos y salidas del equipo, con tablero kanban de proyectos y reportes semanales.

Hecho con Laravel 13 (PHP 8.3+), Blade y Tailwind CSS 4.

## Estado

- **Paso 1 (listo):** login, rol de administrador, alta y edición de empleados, 10 empleados de prueba.
- **Paso 2 (listo):** checador con timer de comida, equipo de hoy, historial y diseño tipo app.
- **Paso 3 (listo):** tablero kanban con proyectos, arrastrar y soltar, y filtros.
- Paso 4: reportes y guía para subir a HostGator.

## Correr en tu compu

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Abre http://localhost:8000 y entra con:

| Usuario | Correo | Contraseña |
| --- | --- | --- |
| Administrador | admin@checador.test | password |
| Developers | ana.lopez@checador.test, carlos.mendez@checador.test, … | password |

Los 10 developers de prueba están en `database/seeders/DatabaseSeeder.php`. Cambia las contraseñas antes de usarlo con el equipo real.

## Base de datos

En local usa SQLite (no necesitas instalar nada). En HostGator se usa la base MySQL que crees en cPanel; solo cambia en `.env`:

```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tuusuario_checador
DB_USERNAME=tuusuario_checador
DB_PASSWORD=...
```

La zona horaria por defecto es `America/Mexico_City` (`APP_TIMEZONE` en `.env`).

## Pruebas

```bash
php artisan test
```
