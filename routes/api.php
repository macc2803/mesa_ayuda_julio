<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeticionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [PeticionController::class, 'dashboard'])->name('dashboard');
    Route::resource('peticiones', PeticionController::class);
});


Route::get('/peticiones/crear', [PeticionController::class, 'create'])->name('peticiones.create');
Route::post('/peticiones', [PeticionController::class, 'store'])->name('peticiones.store');

