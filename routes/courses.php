<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas CRUD de Courses
    Route::resource('courses', CourseController::class);

    // Ruta adicional para obtener niveles de grado (para filtros)
    Route::get('api/courses/grade-levels', [CourseController::class, 'gradeLevels'])
        ->name('courses.grade-levels');
});
