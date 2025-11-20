<?php
 
use App\Http\Controllers\GuardianController;
use Illuminate\Support\Facades\Route;
 
Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas CRUD de Guardians
    Route::resource('guardians', GuardianController::class);
});
