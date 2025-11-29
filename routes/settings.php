<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SettingController;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('user-password.edit');

    Route::put('settings/password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/appearance');
    })->name('appearance.edit');

    Route::get('settings/two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');
     
    // ==================== System Settings ====================
 
    // Rutas de visualización y edición de configuración del sistema
    Route::get('settings/system', [SettingController::class, 'show'])
        ->name('settings.system.show');
 
    Route::get('settings/system/edit', [SettingController::class, 'edit'])
        ->name('settings.system.edit');
 
    Route::patch('settings/system', [SettingController::class, 'update'])
        ->name('settings.system.update');

    Route::post('settings/system/reset', [SettingController::class, 'reset'])
        ->name('settings.system.reset');

    // ==================== System Settings API ====================

    Route::prefix('api/settings')->name('api.settings.')->group(function () {
        
        // Obtener configuración actual
        Route::get('/', [SettingController::class, 'getSettings'])
            ->name('get');
 
        // Actualizar configuración completa
        Route::patch('/', [SettingController::class, 'updateSettings'])
            ->name('update');
 
        // Restaurar a valores por defecto
        Route::post('reset', [SettingController::class, 'resetApi'])
            ->name('reset');
 
        // Validar configuración actual
        Route::get('validate', [SettingController::class, 'validate'])
            ->name('validate');
 
        // Obtener y aplicar configuraciones recomendadas
        Route::get('recommended', [SettingController::class, 'getRecommended'])
            ->name('recommended');
 
        Route::post('apply-recommended', [SettingController::class, 'applyRecommended'])
            ->name('apply-recommended');
 
        // Configuración específica: Late Threshold
        Route::get('late-threshold', [SettingController::class, 'getLateThreshold'])
            ->name('late-threshold.get');
 
        Route::patch('late-threshold', [SettingController::class, 'updateLateThreshold'])
            ->name('late-threshold.update');
 
        // Configuración específica: Auto Notify Parents
        Route::get('auto-notify-parents', [SettingController::class, 'getAutoNotifyParents'])
            ->name('auto-notify-parents.get');
 
        Route::post('auto-notify-parents/toggle', [SettingController::class, 'toggleAutoNotifyParents'])
            ->name('auto-notify-parents.toggle');
 
        Route::patch('auto-notify-parents', [SettingController::class, 'updateAutoNotifyParents'])
            ->name('auto-notify-parents.update');
 
        // Configuración específica: Notification Delay
        Route::get('notification-delay', [SettingController::class, 'getNotificationDelay'])
            ->name('notification-delay.get');
 
        Route::patch('notification-delay', [SettingController::class, 'updateNotificationDelay'])
            ->name('notification-delay.update');
    });
});
