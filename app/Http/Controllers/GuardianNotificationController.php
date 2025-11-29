<?php

namespace App\Http\Controllers;

use App\Models\GuardianNotification;
use App\Models\StudentAttendance;
use App\Models\Guardian;
use App\Http\Requests\StoreGuardianNotificationRequest;
use App\Http\Requests\UpdateGuardianNotificationRequest;
use App\Http\Resources\GuardianNotificationResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuardianNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GuardianNotification::with([
            'attendance.student',
            'attendance.courseSection.course',
            'guardian.user',
        ]);
 
        // Búsqueda por nombre del tutor o estudiante
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('guardian.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('attendance.student', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            });
        }
 
        // Filtro por registro de asistencia
        if ($attendanceId = $request->input('attendance_id')) {
            $query->where('attendance_id', $attendanceId);
        }
 
        // Filtro por tutor
        if ($guardianId = $request->input('guardian_id')) {
            $query->where('guardian_id', $guardianId);
        }
 
        // Filtro por tipo
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }
 
        // Filtro por método
        if ($method = $request->input('method')) {
            $query->where('method', $method);
        }
 
        // Filtro por estado
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
 
        // Filtro por solo pendientes
        if ($request->boolean('pending_only')) {
            $query->where('status', 'pending');
        }
 
        // Filtro por rango de fechas de envío
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('sent_at', '>=', $startDate);
        }
 
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('sent_at', '<=', $endDate);
        }
 
        // Ordenamiento
        $sortBy = $request->input('sort_by', 'created_at');

        $sortOrder = $request->input('sort_order', 'desc');
 
        $query->orderBy($sortBy, $sortOrder);

        $notifications = $query->paginate(15)->withQueryString();

        return Inertia::render('GuardianNotifications/Index', [
            'notifications' => GuardianNotificationResource::collection($notifications),
            'filters' => $request->only([
                'search',
                'attendance_id',
                'guardian_id',
                'type',
                'method',
                'status',
                'pending_only',
                'start_date',
                'end_date',
                'sort_by',
                'sort_order'
            ]),
            'types' => [
                ['value' => 'late', 'label' => 'Tardanza'],
                ['value' => 'absent', 'label' => 'Ausencia'],
                ['value' => 'excused', 'label' => 'Justificación'],
            ],
            'methods' => [
                ['value' => 'email', 'label' => 'Correo electrónico'],
                ['value' => 'sms', 'label' => 'SMS'],
                ['value' => 'whatsapp', 'label' => 'WhatsApp'],
            ],
            'statuses' => [
                ['value' => 'pending', 'label' => 'Pendiente'],
                ['value' => 'sent', 'label' => 'Enviado'],
                ['value' => 'failed', 'label' => 'Fallido'],
            ],
        ]);
    }
 
    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $attendances = StudentAttendance::with([
            'student',
            'courseSection.course',
        ])
        ->whereIn('status', ['late', 'absent', 'excused'])
        ->orderBy('date', 'desc')
        ->get();
 
        $guardians = Guardian::with('user')
            ->whereHas('user', function ($q) {
                $q->where('active', true);
            })
            ->get();
 
        return Inertia::render('GuardianNotifications/Create', [
            'attendances' => $attendances,
            'guardians' => $guardians,
            'types' => [
                ['value' => 'late', 'label' => 'Tardanza'],
                ['value' => 'absent', 'label' => 'Ausencia'],
                ['value' => 'excused', 'label' => 'Justificación'],
            ],
            'methods' => [
                ['value' => 'email', 'label' => 'Correo electrónico'],
                ['value' => 'sms', 'label' => 'SMS'],
                ['value' => 'whatsapp', 'label' => 'WhatsApp'],
            ],
            'statuses' => [
                ['value' => 'pending', 'label' => 'Pendiente'],
                ['value' => 'sent', 'label' => 'Enviado'],
                ['value' => 'failed', 'label' => 'Fallido'],
            ],
        ]);
    }
 
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuardianNotificationRequest $request)
    {
        $notification = GuardianNotification::create($request->validated());
 
        $notification->load(['attendance.student', 'guardian.user']);
 
        return redirect()->route('guardian-notifications.show', $notification)
            ->with('success', 'Notificación creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(GuardianNotification $guardianNotification): Response
    {
        $guardianNotification->load([
            'attendance.student',
            'attendance.courseSection.course',
            'attendance.teacher.user',
            'guardian.user',
        ]);
 
        return Inertia::render('GuardianNotifications/Show', [
            'notification' => GuardianNotificationResource::make($guardianNotification),
        ]);
    }
 
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GuardianNotification $guardianNotification): Response
    {
        $guardianNotification->load([
            'attendance.student',
            'guardian.user',
        ]);
 
        $attendances = StudentAttendance::with([
            'student',
            'courseSection.course',
        ])
        ->whereIn('status', ['late', 'absent', 'excused'])
        ->orderBy('date', 'desc')
        ->get();
 
        $guardians = Guardian::with('user')
            ->whereHas('user', function ($q) {
                $q->where('active', true);
            })
            ->get();
 
        return Inertia::render('GuardianNotifications/Edit', [
            'notification' => GuardianNotificationResource::make($guardianNotification),
            'attendances' => $attendances,
            'guardians' => $guardians,
            'types' => [
                ['value' => 'late', 'label' => 'Tardanza'],
                ['value' => 'absent', 'label' => 'Ausencia'],
                ['value' => 'excused', 'label' => 'Justificación'],
            ],
            'methods' => [
                ['value' => 'email', 'label' => 'Correo electrónico'],
                ['value' => 'sms', 'label' => 'SMS'],
                ['value' => 'whatsapp', 'label' => 'WhatsApp'],
            ],
            'statuses' => [
                ['value' => 'pending', 'label' => 'Pendiente'],
                ['value' => 'sent', 'label' => 'Enviado'],
                ['value' => 'failed', 'label' => 'Fallido'],
            ],
        ]);
    }
 
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuardianNotificationRequest $request, GuardianNotification $guardianNotification)
    {
        $guardianNotification->update($request->validated());
 
        return redirect()->route('guardian-notifications.show', $guardianNotification)
            ->with('success', 'Notificación actualizada exitosamente.');
    }
 
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GuardianNotification $guardianNotification)
    {
        $guardianNotification->delete();
 
        return redirect()->route('guardian-notifications.index')
            ->with('success', 'Notificación eliminada exitosamente.');
    }
 
    /**
     * Get notifications by attendance.
     */
    public function byAttendance(StudentAttendance $attendance)
    {
        $notifications = GuardianNotification::where('attendance_id', $attendance->id)
            ->with(['guardian.user'])
            ->orderBy('created_at', 'desc')
            ->get();
 
        return response()->json([
            'data' => GuardianNotificationResource::collection($notifications),
        ]);
    }
 
    /**
     * Get notifications by guardian.
     */
    public function byGuardian(Guardian $guardian)
    {
        $notifications = GuardianNotification::where('guardian_id', $guardian->id)
            ->with(['attendance.student', 'attendance.courseSection.course'])
            ->orderBy('created_at', 'desc')
            ->get();
 
        return response()->json([
            'data' => GuardianNotificationResource::collection($notifications),
        ]);
    }
 
    /**
     * Get pending notifications.
     */
    public function pending()
    {
        $notifications = GuardianNotification::where('status', 'pending')
            ->with(['attendance.student', 'guardian.user'])
            ->orderBy('created_at', 'asc')
            ->get();
 
        return response()->json([
            'data' => GuardianNotificationResource::collection($notifications),
        ]);
    }
 
    /**
     * Mark notification as sent.
     */
    public function markAsSent(GuardianNotification $guardianNotification)
    {
        $guardianNotification->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
 
        return response()->json([
            'message' => 'Notificación marcada como enviada.',
            'data' => GuardianNotificationResource::make($guardianNotification),
        ]);
    }
 
    /**
     * Mark notification as failed
     */
    public function markAsFailed(Request $request, GuardianNotification $guardianNotification)
    {
        $request->validate([
            'error_message' => 'nullable|string|max:1000',
        ]);
 
        $guardianNotification->update([
            'status' => 'failed',
            'sent_at' => null,
            'message' => $request->error_message ?? $guardianNotification->message,
        ]);
 
        return response()->json([
            'message' => 'Notificación marcada como fallida.',
            'data' => GuardianNotificationResource::make($guardianNotification),
        ]);
    }
 
    /**
     * Retry sending a failed notification.
     */
    public function retry(GuardianNotification $guardianNotification)
    {
        if ($guardianNotification->status !== 'failed') {
            return response()->json([
                'message' => 'Solo se pueden reintentar notificaciones fallidas.',
            ], 422);
        }
 
        $guardianNotification->update([
            'status' => 'pending',
            'sent_at' => null,
        ]);
 
        return response()->json([
            'message' => 'Notificación marcada para reintento.',
            'data' => GuardianNotificationResource::make($guardianNotification),
        ]);
    }
 
    /**
     * Bulk send pending notifications.
     */
    public function bulkSend(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array|min:1',
            'notification_ids.*' => 'required|exists:guardian_notifications,id',
        ]);
 
        $sent = [];
        $failed = [];
 
        foreach ($request->notification_ids as $notificationId) {
            $notification = GuardianNotification::find($notificationId);
 
            if (!$notification || $notification->status !== 'pending') {
                $failed[] = [
                    'id' => $notificationId,
                    'message' => 'La notificación no está pendiente.',
                ];
                continue;
            }
 
            // Aquí iría la lógica real de envío (email, SMS, WhatsApp)
            // Por ahora solo marcamos como enviado
            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
 
            $sent[] = $notificationId;
        }
 
        return response()->json([
            'message' => count($sent) > 0
                ? sprintf('Se enviaron %d notificación(es) exitosamente.', count($sent))
                : 'No se envió ninguna notificación.',
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }
 
    /**
     * Get notification statistics.
     */
    public function statistics(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'guardian_id' => 'nullable|exists:guardians,id',
        ]);
 
        $query = GuardianNotification::query();
 
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date,
            ]);
        }
 
        if ($request->guardian_id) {
            $query->where('guardian_id', $request->guardian_id);
        }
 
        $statistics = [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'sent' => (clone $query)->where('status', 'sent')->count(),
            'failed' => (clone $query)->where('status', 'failed')->count(),
            'by_type' => [
                'late' => (clone $query)->where('type', 'late')->count(),
                'absent' => (clone $query)->where('type', 'absent')->count(),
                'excused' => (clone $query)->where('type', 'excused')->count(),
            ],
            'by_method' => [
                'email' => (clone $query)->where('method', 'email')->count(),
                'sms' => (clone $query)->where('method', 'sms')->count(),
                'whatsapp' => (clone $query)->where('method', 'whatsapp')->count(),
            ],
        ];
 
        $statistics['success_rate'] = $statistics['total'] > 0
            ? round($statistics['sent'] / $statistics['total'] * 100, 2)
            : 0;
 
        return response()->json($statistics);
    }
}
