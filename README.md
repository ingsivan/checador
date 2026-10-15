# Checador

App web para que el equipo registre su llegada, su salida a comer, su regreso y su salida, con timer de comida, tablero kanban de proyectos y reportes semanales de quién tarda más en comer.

Hecha con Laravel 13 (PHP 8.3+), Blade y Tailwind CSS 4. En el celular se ve y se instala como una app (pantalla completa, pestañas abajo, ícono propio).

## Qué incluye

| Paso | Qué hace |
| --- | --- |
| 1 | Login, rol de administrador y registro de empleados con horario. |
| 2 | Checador con un botón para cada momento del día, timer de comida, Equipo hoy, Mi historial y la guía “Cómo funciona”. |
| 3 | Tablero kanban (Pendiente, En progreso, En revisión, Terminado) con arrastrar y soltar, filtros y administración de proyectos. |
| 4 | Reportes semanales: ranking de comida, llegadas tarde, días sin salida, descarga a Excel (CSV) y corrección de registros por el admin. |

Reglas configurables en `.env`:

```
CHECADOR_LUNCH_MINUTES=60     # minutos de comida permitidos
CHECADOR_LATE_TOLERANCE=10    # minutos de tolerancia para la llegada
APP_TIMEZONE=America/Mexico_City
```

---

## Instalar en tu computadora (local)

Necesitas PHP 8.3 o más nuevo, Composer y Node.js 20 o más nuevo.

```bash
git clone https://github.com/ingsivan/checador.git
cd checador
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite     # en Windows: crea el archivo vacío a mano
php artisan migrate --seed         # crea las tablas y los datos de prueba
npm run build
php artisan serve
```

Abre http://localhost:8000 y entra con:

| Usuario | Correo | Contraseña |
| --- | --- | --- |
| Administrador | admin@checador.test | password |
| Developers | ana.lopez@checador.test, carlos.mendez@checador.test, daniela.ruiz@checador.test, … | password |

Los datos de prueba traen dos semanas de registros, 4 proyectos y tarjetas en el tablero. Para volver a empezar: `php artisan migrate:fresh --seed`.

Para probar en tu celular dentro de la misma red: `php artisan serve --host=0.0.0.0` y abre `http://IP-DE-TU-COMPU:8000`.

Pruebas automáticas: `php artisan test`.

---

## Subir a HostGator (cPanel)

La carpeta `public/build` ya va dentro del repositorio, así que **en el servidor no necesitas Node.js**.

### 1. Prepara cPanel

1. **PHP 8.3:** cPanel → *MultiPHP Manager* → elige tu dominio → PHP 8.3 → *Apply*.
2. **Base de datos:** cPanel → *MySQL® Databases*:
   - Crea la base (queda como `tuusuario_checador`).
   - Crea un usuario con contraseña segura.
   - En *Add User To Database* une usuario y base con **ALL PRIVILEGES**.
3. **Subdominio (recomendado):** cPanel → *Domains* → *Create A New Domain* → `checador.tudominio.com`. En *Document Root* escribe `checador/public`.
4. **SSH:** cPanel → *SSH Access* (o *Terminal*). Si no aparece, pídele a soporte de HostGator que lo active; lo usarás para Composer y Artisan.

### 2. Sube el código

Opción A, con Git (recomendada):

```bash
cd ~
git clone https://github.com/ingsivan/checador.git checador
```

Si el repo es privado, usa cPanel → *Git™ Version Control* → *Create* y pega la URL con un token de GitHub, o usa la opción B.

Opción B, sin Git: en GitHub descarga *Code → Download ZIP*, súbelo con el *File Manager* a tu carpeta de inicio (fuera de `public_html`), extráelo y renombra la carpeta a `checador`.

### 3. Instala y configura

Desde la Terminal de cPanel o por SSH:

```bash
cd ~/checador
composer install --no-dev --optimize-autoloader
# si "composer" no existe:  curl -sS https://getcomposer.org/installer | php  &&  php composer.phar install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

Edita `.env` (con `nano .env` o desde el File Manager):

```
APP_NAME=Checador
APP_ENV=production
APP_DEBUG=false
APP_URL=https://checador.tudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tuusuario_checador
DB_USERNAME=tuusuario_checadoruser
DB_PASSWORD=la-contraseña-que-creaste
```

Crea las tablas, tu usuario administrador y deja todo optimizado:

```bash
php artisan migrate --force
php artisan checador:admin "Tu Nombre" tu@correo.com    # te pedirá la contraseña
chmod -R 775 storage bootstrap/cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Si solo quieres ver la demo en línea, en lugar de `checador:admin` corre `php artisan db:seed --force` (carga los empleados de prueba con contraseña `password`; no lo hagas con datos reales).

### 4. HTTPS y listo

- cPanel → *SSL/TLS Status* → *Run AutoSSL* para tener `https://`.
- Entra a `https://checador.tudominio.com`, inicia sesión como admin y da de alta a tu equipo en **Más → Empleados** y tus proyectos en **Más → Proyectos**.
- Cada developer instala la app en su celular siguiendo **Más → Cómo funciona → Instálala en tu celular**.

### Si tiene que ir en el dominio principal (public_html)

1. Deja el proyecto en `~/checador`.
2. Copia **el contenido** de `~/checador/public` a `~/public_html`.
3. En `~/public_html/index.php` cambia las rutas `__DIR__.'/../` por `__DIR__.'/../checador/` (son tres líneas: `storage/framework/maintenance.php`, `vendor/autoload.php` y `bootstrap/app.php`).

### Actualizar después

```bash
cd ~/checador
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## Problemas comunes

| Síntoma | Solución |
| --- | --- |
| Error 500 en blanco | Revisa `storage/logs/laravel.log`. Casi siempre es el `.env` (base de datos) o permisos de `storage`. |
| “Vite manifest not found” | Falta `public/build`. Corre `npm run build` en tu compu y súbelo. |
| Se ve sin estilos | `APP_URL` no coincide con la dirección real (http vs https). |
| La hora sale mal | Revisa `APP_TIMEZONE` y corre `php artisan config:cache`. |
| “could not find driver” | En *Select PHP Version* activa la extensión `pdo_mysql`. |
