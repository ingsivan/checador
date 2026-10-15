<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ClockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
});

Route::view('/como-funciona', 'help')->name('help');

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('/checar', ClockController::class)->name('clock');
    Route::get('/equipo', TeamController::class)->name('team');
    Route::get('/historial', HistoryController::class)->name('history');
    Route::view('/mas', 'more')->name('more');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/tablero', BoardController::class)->name('board');
    Route::post('/tareas', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tareas/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tareas/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tareas/{task}/mover', [TaskController::class, 'move'])->name('tasks.move');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('employees', EmployeeController::class)->except(['show', 'destroy']);
        Route::patch('employees/{employee}/toggle', [EmployeeController::class, 'toggle'])->name('employees.toggle');
        Route::resource('projects', ProjectController::class)->except(['show', 'destroy']);
        Route::get('reportes', [ReportController::class, 'index'])->name('reports');
        Route::get('reportes/csv', [ReportController::class, 'export'])->name('reports.export');
        Route::get('asistencias/{user}', [AttendanceController::class, 'edit'])->name('attendance.edit');
        Route::put('asistencias/{user}/{date}', [AttendanceController::class, 'update'])
            ->where('date', '\d{4}-\d{2}-\d{2}')->name('attendance.update');
    });
});
