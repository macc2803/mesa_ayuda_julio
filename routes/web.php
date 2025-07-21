<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PeticionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\LogController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;

//
// Página principal
//
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

//
// Rutas protegidas para usuarios verificados
//
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [PeticionController::class, 'dashboard'])->name('dashboard');

    // Para usuario
    Route::get('/mis-peticiones', [PeticionController::class, 'misPeticiones'])->name('peticiones.mias');
    Route::get('/peticiones/crear', [PeticionController::class, 'create'])->name('peticiones.create');
    Route::post('/peticiones', [PeticionController::class, 'store'])->name('peticiones.store');

    // Para encargado
    Route::get('/peticiones', [PeticionController::class, 'index'])->name('peticiones.index');
    Route::put('/peticiones/{peticion}', [PeticionController::class, 'update'])->name('peticiones.update');

    // Actualizar estado
    Route::put('/peticiones/{id}/estado', [PeticionController::class, 'actualizarEstado'])->name('peticiones.actualizarEstado');
});

//
    // Ruta de prueba para ver roles de los usuarios
    //
    Route::get('/ver-roles', function () {
        return User::select('id', 'name', 'role')->get();
    });

//
// RUTAS DE VERIFICACIÓN DE CORREO ELECTRÓNICO
//

// Muestra la página de verificación
Route::get('/email/verify', function () {
    return Inertia::render('Auth/VerifyEmail');
})->middleware('auth')->name('verification.notice');

// Procesa el enlace de verificación recibido en el correo
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard'); // Redirige después de verificar
})->middleware(['auth', 'signed'])->name('verification.verify');

// Permite reenviar el correo de verificación
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '¡Enlace de verificación enviado!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');



//Databases
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/databases', [DatabaseController::class, 'index'])->name('databases.index');
    Route::get('/databases/{table}', [DatabaseController::class, 'show'])->name('databases.show');
    Route::post('/databases/{table}', [DatabaseController::class, 'store'])->name('databases.store');
    Route::put('/databases/{table}/{id}', [DatabaseController::class, 'update'])->name('databases.update');
    Route::delete('/databases/{table}/{id}', [DatabaseController::class, 'destroy'])->name('databases.destroy');
    //Logs 
    Route::get('/logs',[LogController::class,'index'])->name('logs.index')->middleware('auth');

});