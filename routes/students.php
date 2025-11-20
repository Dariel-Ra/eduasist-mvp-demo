<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas CRUD de Students
    Route::resource('students', StudentController::class);

    // Rutas adicionales para obtener opciones de filtro
    Route::get('api/students/grade-levels', [StudentController::class, 'gradeLevels'])
        ->name('students.grade-levels');

    Route::get('api/students/sections', [StudentController::class, 'sections'])
        ->name('students.sections');
});
