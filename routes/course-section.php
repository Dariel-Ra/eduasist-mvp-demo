<?php

 

use App\Http\Controllers\CourseSectionController;

use App\Http\Controllers\CourseSectionStudentController;

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // ==================== Course Sections ====================
 
    // Rutas CRUD de Course Sections
    Route::resource('course-sections', CourseSectionController::class);
 
    // Rutas adicionales para Course Sections
    Route::prefix('api/course-sections')->name('course-sections.')->group(function () {

        // Obtener secciones por curso
        Route::get('by-course/{course}', [CourseSectionController::class, 'byCourse'])
            ->name('by-course');

        // Obtener secciones por profesor
        Route::get('by-teacher/{teacher}', [CourseSectionController::class, 'byTeacher'])
            ->name('by-teacher');
 
        // Obtener secciones por día de la semana
        Route::get('by-day', [CourseSectionController::class, 'byDay'])
            ->name('by-day');
 
        // Obtener secciones actualmente en sesión
        Route::get('in-session', [CourseSectionController::class, 'inSession'])
            ->name('in-session');
    });
 
    // Rutas para gestionar estudiantes en una sección
    Route::prefix('course-sections/{courseSection}')->name('course-sections.')->group(function () {
        // Inscribir estudiante
        Route::post('enroll-student', [CourseSectionController::class, 'enrollStudent'])
            ->name('enroll-student');
 
        // Retirar estudiante
        Route::post('unenroll-student', [CourseSectionController::class, 'unenrollStudent'])
            ->name('unenroll-student');
 
        // Actualizar estado del estudiante
        Route::patch('update-student-status', [CourseSectionController::class, 'updateStudentStatus'])
            ->name('update-student-status');
    });
 
    // ==================== Course Section Students ====================
 
    // Rutas CRUD de Course Section Students
    Route::resource('course-section-students', CourseSectionStudentController::class);
 
    // Rutas adicionales para Course Section Students
    Route::prefix('api/course-section-students')->name('course-section-students.')->group(function () {

        // Obtener inscripciones por sección
        Route::get('by-course-section/{courseSection}', [CourseSectionStudentController::class, 'byCourseSection'])
            ->name('by-course-section');
 
        // Obtener inscripciones por estudiante
        Route::get('by-student/{student}', [CourseSectionStudentController::class, 'byStudent'])
            ->name('by-student');
 
        // Obtener inscripciones activas por estudiante
        Route::get('active-by-student/{student}', [CourseSectionStudentController::class, 'activeByStudent'])
            ->name('active-by-student');
 
        // Inscripción masiva de estudiantes
        Route::post('bulk-enroll', [CourseSectionStudentController::class, 'bulkEnroll'])
            ->name('bulk-enroll');
 
        // Actualización masiva de estado
        Route::patch('bulk-update-status', [CourseSectionStudentController::class, 'bulkUpdateStatus'])
            ->name('bulk-update-status');
    });
});