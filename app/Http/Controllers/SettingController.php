<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
    $setting = Setting::getInstance();

        return Inertia::render('Settings/System', [
            'setting' => SettingResource::make($setting),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        $setting = Setting::getInstance();
 
        return Inertia::render('Settings/SystemEdit', [
            'setting' => SettingResource::make($setting),
            'defaults' => [
                'late_threshold_minutes' => 15,
                'auto_notify_parents' => true,
                'notification_delay_minutes' => 30,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSettingRequest $request, Setting $setting)
    {
        $setting = Setting::getInstance();
        $setting->update($request->validated());

        return redirect()->route('settings.system.show')

            ->with('success', 'Configuración actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }

    /**
     * Get the current settings as JSON (API).
     */
    public function getSettings()
    {
        $setting = Setting::getInstance();
 
        return response()->json([
            'data' => SettingResource::make($setting),
        ]);
    }


    /**
     * Update the settings via API.
     */
    public function updateSettings(UpdateSettingRequest $request)
    {
        $setting = Setting::getInstance();
        $setting->update($request->validated());
 
        return response()->json([
            'message' => 'Configuración actualizada exitosamente.',
            'data' => SettingResource::make($setting),
        ]);
    }
 
    /**
     * Reset settings to default values.
     */
    public function reset()
    {
        $setting = Setting::getInstance();
 
        $setting->update([
            'late_threshold_minutes' => 15,
            'auto_notify_parents' => true,
            'notification_delay_minutes' => 30,
        ]);
 
        return redirect()->route('settings.system.show')
            ->with('success', 'Configuración restaurada a valores por defecto.');
    }
 
    /**
     * Reset settings to default values via API.
     */
    public function resetApi()
    {
        $setting = Setting::getInstance();
 
        $setting->update([
            'late_threshold_minutes' => 15,
            'auto_notify_parents' => true,
            'notification_delay_minutes' => 30,
        ]);
 
        return response()->json([
            'message' => 'Configuración restaurada a valores por defecto.',
            'data' => SettingResource::make($setting),
        ]);
    }
 
    /**
     * Get late threshold in minutes.
     */
    public function getLateThreshold()
    {
        $setting = Setting::getInstance();
 
        return response()->json([
            'late_threshold_minutes' => $setting->late_threshold_minutes,
        ]);
    }
 
    /**
     * Update late threshold.
     */
    public function updateLateThreshold(Request $request)
    {
        $request->validate([
            'late_threshold_minutes' => [
                'required',
                'integer',
                'min:1',
                'max:180',
            ],
        ]);
 
        $setting = Setting::getInstance();
        $setting->update([
            'late_threshold_minutes' => $request->late_threshold_minutes,
        ]);
 
        return response()->json([
            'message' => 'Umbral de tardanza actualizado exitosamente.',
            'late_threshold_minutes' => $setting->late_threshold_minutes,
        ]);
    }
 
    /**
     * Get auto notify parents setting.
     */
    public function getAutoNotifyParents()
    {
        $setting = Setting::getInstance();
 
        return response()->json([
            'auto_notify_parents' => $setting->auto_notify_parents,
        ]);
    }
 
    /**
     * Toggle auto notify parents.
     */
    public function toggleAutoNotifyParents()
    {
        $setting = Setting::getInstance();
 
        $setting->update([
            'auto_notify_parents' => !$setting->auto_notify_parents,
        ]);
 
        return response()->json([
            'message' => $setting->auto_notify_parents
                ? 'Notificación automática activada.'
                : 'Notificación automática desactivada.',
            'auto_notify_parents' => $setting->auto_notify_parents,
        ]);
    }
 
    /**
     * Update auto notify parents setting.
     */
    public function updateAutoNotifyParents(Request $request)
    {
        $request->validate([
            'auto_notify_parents' => [
                'required',
                'boolean',
            ],
        ]);
 
        $setting = Setting::getInstance();
        $setting->update([
            'auto_notify_parents' => $request->auto_notify_parents,
        ]);
 
        return response()->json([
            'message' => 'Configuración de notificación automática actualizada.',
            'auto_notify_parents' => $setting->auto_notify_parents,
        ]);
    }
 
    /**
     * Get notification delay in minutes.
     */
    public function getNotificationDelay()
    {
        $setting = Setting::getInstance();
 
        return response()->json([
            'notification_delay_minutes' => $setting->notification_delay_minutes,
        ]);
    }
 
    /**
     * Update notification delay.
     */
    public function updateNotificationDelay(Request $request)
    {
        $request->validate([
            'notification_delay_minutes' => [
                'required',
                'integer',
                'min:0',
                'max:240',
            ],
        ]);
 
        $setting = Setting::getInstance();
        $setting->update([
            'notification_delay_minutes' => $request->notification_delay_minutes,
        ]);
 
        return response()->json([
            'message' => 'Retraso de notificación actualizado exitosamente.',
            'notification_delay_minutes' => $setting->notification_delay_minutes,
        ]);
    }
 
    /**
     * Validate settings configuration.
     */
    public function validate()
    {
        $setting = Setting::getInstance();
 
        $issues = [];
        $warnings = [];
 
        // Validar umbral de tardanza
        if ($setting->late_threshold_minutes < 5) {
            $warnings[] = [
                'field' => 'late_threshold_minutes',
                'message' => 'El umbral de tardanza es muy bajo. Se recomienda al menos 5 minutos.',
            ];
        } elseif ($setting->late_threshold_minutes > 30) {
            $warnings[] = [
                'field' => 'late_threshold_minutes',
                'message' => 'El umbral de tardanza es alto. Podría ser demasiado permisivo.',
            ];
        }
 
        // Validar retraso de notificación
        if ($setting->notification_delay_minutes < $setting->late_threshold_minutes) {
            $issues[] = [
                'field' => 'notification_delay_minutes',
                'message' => 'El retraso de notificación es menor al umbral de tardanza. Esto puede causar notificaciones prematuras.',
            ];
        }
 
        if ($setting->notification_delay_minutes < 15) {
            $warnings[] = [
                'field' => 'notification_delay_minutes',
                'message' => 'El retraso de notificación es corto. Los tutores podrían recibir muchas notificaciones.',
            ];
        }
 
        // Validar notificación automática
        if (!$setting->auto_notify_parents) {
            $warnings[] = [
                'field' => 'auto_notify_parents',
                'message' => 'La notificación automática está desactivada. Los tutores no recibirán alertas.',
            ];
        }
 
        return response()->json([
            'valid' => empty($issues),
            'issues' => $issues,
            'warnings' => $warnings,
            'setting' => SettingResource::make($setting),
        ]);
    }
 
    /**
     * Get recommended settings based on school type.
     */
    public function getRecommended(Request $request)
    {
        $request->validate([
            'school_type' => 'required|in:strict,moderate,flexible',
        ]);
 
        $recommendations = match($request->school_type) {
            'strict' => [
                'late_threshold_minutes' => 5,
                'auto_notify_parents' => true,
                'notification_delay_minutes' => 10,
                'description' => 'Configuración estricta: umbral bajo y notificaciones rápidas.',
            ],
            'moderate' => [
                'late_threshold_minutes' => 15,
                'auto_notify_parents' => true,
                'notification_delay_minutes' => 30,
                'description' => 'Configuración moderada: valores balanceados (por defecto).',
            ],
            'flexible' => [
                'late_threshold_minutes' => 30,
                'auto_notify_parents' => true,
                'notification_delay_minutes' => 60,
                'description' => 'Configuración flexible: más permisiva con los retrasos.',
            ],
        };
 
        return response()->json($recommendations);
    }
 
    /**
     * Apply recommended settings.
     */
    public function applyRecommended(Request $request)
    {
        $request->validate([
            'school_type' => 'required|in:strict,moderate,flexible',
        ]);
 
        $values = match($request->school_type) {
            'strict' => [
                'late_threshold_minutes' => 5,
                'auto_notify_parents' => true,
                'notification_delay_minutes' => 10,
            ],
            'moderate' => [
                'late_threshold_minutes' => 15,
                'auto_notify_parents' => true,
                'notification_delay_minutes' => 30,
            ],
            'flexible' => [
                'late_threshold_minutes' => 30,
                'auto_notify_parents' => true,
                'notification_delay_minutes' => 60,
            ],
        };
 
        $setting = Setting::getInstance();
        $setting->update($values);
 
        return response()->json([
            'message' => 'Configuración aplicada exitosamente.',
            'data' => SettingResource::make($setting),
        ]);
    }
}
