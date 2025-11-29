<?php
 
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\GuardianNotificationController;
use Illuminate\Support\Facades\Route;
 
Route::middleware(['auth', 'verified'])->group(function () {

    // ==================== Student Attendances ====================
 
    // Rutas CRUD de Student Attendances
    Route::resource('student-attendances', StudentAttendanceController::class);
 
    // Rutas adicionales para Student Attendances
    Route::prefix('api/student-attendances')->name('student-attendances.')->group(function () {

        // Obtener asistencias por sección
        Route::get('by-course-section/{courseSection}', [StudentAttendanceController::class, 'byCourseSection'])
            ->name('by-course-section');
 
        // Obtener asistencias por estudiante
        Route::get('by-student/{student}', [StudentAttendanceController::class, 'byStudent'])
            ->name('by-student');
 
        // Obtener asistencias del día actual
        Route::get('today', [StudentAttendanceController::class, 'today'])
            ->name('today');
 
        // Obtener estadísticas de asistencia
        Route::get('statistics', [StudentAttendanceController::class, 'statistics'])
            ->name('statistics');
 
        // Crear asistencias masivamente
        Route::post('bulk-create', [StudentAttendanceController::class, 'bulkCreate'])
            ->name('bulk-create');
    });
 
    // ==================== Guardian Notifications ====================
 
    // Rutas CRUD de Guardian Notifications
    Route::resource('guardian-notifications', GuardianNotificationController::class);
 
    // Rutas adicionales para Guardian Notifications
    Route::prefix('api/guardian-notifications')->name('guardian-notifications.')->group(function () {

        // Obtener notificaciones por asistencia
        Route::get('by-attendance/{attendance}', [GuardianNotificationController::class, 'byAttendance'])
            ->name('by-attendance');
 
        // Obtener notificaciones por tutor
        Route::get('by-guardian/{guardian}', [GuardianNotificationController::class, 'byGuardian'])
            ->name('by-guardian');
 
        // Obtener notificaciones pendientes
        Route::get('pending', [GuardianNotificationController::class, 'pending'])
            ->name('pending');
 
        // Obtener estadísticas de notificaciones
        Route::get('statistics', [GuardianNotificationController::class, 'statistics'])
            ->name('statistics');
    });
 
    // Rutas para gestionar estado de notificaciones
    Route::prefix('guardian-notifications/{guardianNotification}')->name('guardian-notifications.')->group(function () {
        // Marcar como enviada
        Route::patch('mark-as-sent', [GuardianNotificationController::class, 'markAsSent'])
            ->name('mark-as-sent');
 
        // Marcar como fallida
        Route::patch('mark-as-failed', [GuardianNotificationController::class, 'markAsFailed'])
            ->name('mark-as-failed');
 
        // Reintentar envío
        Route::post('retry', [GuardianNotificationController::class, 'retry'])
            ->name('retry');
    });
 
    // Envío masivo de notificaciones
    Route::post('api/guardian-notifications/bulk-send', [GuardianNotificationController::class, 'bulkSend'])
        ->name('guardian-notifications.bulk-send');
});