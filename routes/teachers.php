<?php

use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas CRUD de Teachers
    Route::resource('teachers', TeacherController::class);

    // Ruta adicional para obtener especialidades (para filtros)
    Route::get('api/teachers/specialties', [TeacherController::class, 'specialties'])
        ->name('teachers.specialties');
});
