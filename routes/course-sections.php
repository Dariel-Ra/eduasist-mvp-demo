<?php

use App\Http\Controllers\CourseSectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas CRUD de Course Sections
    Route::resource('course-sections', CourseSectionController::class);

    // Rutas adicionales para filtros y consultas
    Route::get('api/course-sections/by-course/{course}', [CourseSectionController::class, 'byCourse'])
        ->name('course-sections.by-course');

    Route::get('api/course-sections/by-teacher/{teacher}', [CourseSectionController::class, 'byTeacher'])
        ->name('course-sections.by-teacher');

    Route::get('api/course-sections/available-days', [CourseSectionController::class, 'availableDays'])
        ->name('course-sections.available-days');
});
