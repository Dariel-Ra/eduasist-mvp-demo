<?php

use App\Http\Controllers\GuardianStudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    // Rutas CRUD de GuardianStudent
    Route::resource('guardian-student', GuardianStudentController::class);

    // Rutas adicionales para consultas específicas
    Route::get('api/guardian-student/by-guardian/{guardian}', [GuardianStudentController::class, 'byGuardian'])
        ->name('guardian-student.by-guardian');

    Route::get('api/guardian-student/by-student/{student}', [GuardianStudentController::class, 'byStudent'])
        ->name('guardian-student.by-student');

    // Ruta para establecer contacto primario
    Route::patch('api/guardian-student/{guardianStudent}/set-primary', [GuardianStudentController::class, 'setPrimary'])
        ->name('guardian-student.set-primary');
});
