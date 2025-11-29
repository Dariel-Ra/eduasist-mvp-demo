<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendance;
use App\Models\CourseSection;
use App\Models\Student;
use App\Models\Teacher;
use App\Http\Requests\StoreStudentAttendanceRequest;
use App\Http\Requests\UpdateStudentAttendanceRequest;
use App\Http\Resources\StudentAttendanceResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StudentAttendance::with([
            'courseSection.course',
            'student',
            'teacher.user'
        ]);
 
        // Búsqueda por nombre del estudiante o curso
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('enrollment_code', 'like', "%{$search}%");
                })
                ->orWhereHas('courseSection.course', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            });
        }
 
        // Filtro por sección del curso
        if ($courseSectionId = $request->input('course_section_id')) {
            $query->where('course_section_id', $courseSectionId);
        }
 
        // Filtro por estudiante
        if ($studentId = $request->input('student_id')) {
            $query->where('student_id', $studentId);
        }
 
        // Filtro por profesor
        if ($teacherId = $request->input('teacher_id')) {
            $query->where('teacher_id', $teacherId);
        }
 
        // Filtro por estado
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
 
        // Filtro por fecha
        if ($date = $request->input('date')) {
            $query->whereDate('date', $date);
        }
 
        // Filtro por rango de fechas
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('date', '>=', $startDate);
        }
 
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('date', '<=', $endDate);
        }
 
        // Filtro por solo hoy
        if ($request->boolean('today')) {
            $query->whereDate('date', today());
        }

 
        // Ordenamiento
        $sortBy = $request->input('sort_by', 'date');
        $sortOrder = $request->input('sort_order', 'desc');
 
        $query->orderBy($sortBy, $sortOrder);
 
        $attendances = $query
            ->withCount('notifications')
            ->paginate(15)
            ->withQueryString();
 
        return Inertia::render('StudentAttendances/Index', [
            'attendances' => StudentAttendanceResource::collection($attendances),
            'filters' => $request->only([
                'search',
                'course_section_id',
                'student_id',
                'teacher_id',
                'status',
                'date',
                'start_date',
                'end_date',
                'today',
                'sort_by',
                'sort_order'
            ]),
            'statuses' => [
                ['value' => 'present', 'label' => 'Presente'],
                ['value' => 'late', 'label' => 'Tardanza'],
                ['value' => 'absent', 'label' => 'Ausente'],
                ['value' => 'excused', 'label' => 'Justificado'],
            ],
        ]);
    }
 
    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $courseSections = CourseSection::with(['course', 'teacher.user'])
            ->active()
            ->get();
 
        $students = Student::where('active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
 
        $teachers = Teacher::with('user')
            ->whereHas('user', function ($q) {
                $q->where('active', true);
            })
            ->get();
 
        return Inertia::render('StudentAttendances/Create', [
            'course_sections' => $courseSections,
            'students' => $students,
            'teachers' => $teachers,
            'statuses' => [
                ['value' => 'present', 'label' => 'Presente'],
                ['value' => 'late', 'label' => 'Tardanza'],
                ['value' => 'absent', 'label' => 'Ausente'],
                ['value' => 'excused', 'label' => 'Justificado'],
            ],
        ]);
    }
 
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentAttendanceRequest $request)
    {
        $attendance = StudentAttendance::create($request->validated());

        $attendance->load(['courseSection.course', 'student', 'teacher']);
 
        // Crear notificaciones automáticas si el estado lo requiere
        if (in_array($attendance->status, ['late', 'absent'])) {
            $this->createNotificationsForAttendance($attendance);
        }
 
        return redirect()->route('student-attendances.show', $attendance)
            ->with('success', 'Asistencia registrada exitosamente.');
    }
 
    /**
     * Display the specified resource.
     */
    public function show(StudentAttendance $studentAttendance): Response
    {
        $studentAttendance->load([
            'courseSection.course',
            'courseSection.teacher.user',
            'student.guardians',
            'teacher.user',
            'notifications.guardian.user',
        ]);
 
        return Inertia::render('StudentAttendances/Show', [
            'attendance' => StudentAttendanceResource::make($studentAttendance),
        ]);
    }
 
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentAttendance $studentAttendance): Response
    {
        $studentAttendance->load([
            'courseSection.course',
            'student',
            'teacher',
        ]);
 
        $courseSections = CourseSection::with(['course', 'teacher.user'])
            ->active()
            ->get();
 
        $students = Student::where('active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
 
        $teachers = Teacher::with('user')
            ->whereHas('user', function ($q) {
                $q->where('active', true);
            })
            ->get();
 
        return Inertia::render('StudentAttendances/Edit', [
            'attendance' => StudentAttendanceResource::make($studentAttendance),
            'course_sections' => $courseSections,
            'students' => $students,
            'teachers' => $teachers,
            'statuses' => [
                ['value' => 'present', 'label' => 'Presente'],
                ['value' => 'late', 'label' => 'Tardanza'],
                ['value' => 'absent', 'label' => 'Ausente'],
                ['value' => 'excused', 'label' => 'Justificado'],
            ],
        ]);
    }
 
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentAttendanceRequest $request, StudentAttendance $studentAttendance)
    {
        $oldStatus = $studentAttendance->status;
        $studentAttendance->update($request->validated());
 
        // Crear notificaciones automáticas si el estado cambió y lo requiere
        if ($oldStatus !== $studentAttendance->status && in_array($studentAttendance->status, ['late', 'absent'])) {
            $this->createNotificationsForAttendance($studentAttendance);
        }
 
        return redirect()->route('student-attendances.show', $studentAttendance)
            ->with('success', 'Asistencia actualizada exitosamente.');
    }
 
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAttendance $studentAttendance)
    {
        $studentAttendance->delete();
 
        return redirect()->route('student-attendances.index')
            ->with('success', 'Registro de asistencia eliminado exitosamente.');
    }
 
    /**
     * Get attendances by course section.
     */
    public function byCourseSection(CourseSection $courseSection)
    {
        $attendances = StudentAttendance::where('course_section_id', $courseSection->id)
            ->with(['student', 'teacher.user'])
            ->orderBy('date', 'desc')
            ->get();
 
        return response()->json([
            'data' => StudentAttendanceResource::collection($attendances),
        ]);
    }
 
    /**
     * Get attendances by student.
     */
    public function byStudent(Student $student)
    {
        $attendances = StudentAttendance::where('student_id', $student->id)
            ->with(['courseSection.course', 'teacher.user'])
            ->orderBy('date', 'desc')
            ->get();
 
        return response()->json([
            'data' => StudentAttendanceResource::collection($attendances),
        ]);
    }
 
    /**
     * Get today's attendances.
     */
    public function today()
    {
        $attendances = StudentAttendance::whereDate('date', today())
            ->with(['courseSection.course', 'student', 'teacher.user'])
            ->orderBy('check_in_time', 'desc')
            ->get();
 
        return response()->json([
            'data' => StudentAttendanceResource::collection($attendances),
        ]);
    }
 
    /**
     * Get attendance statistics for a date range.
     */
    public function statistics(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'course_section_id' => 'nullable|exists:course_sections,id',
            'student_id' => 'nullable|exists:students,id',
        ]);
 
        $query = StudentAttendance::whereBetween('date', [
            $request->start_date,
            $request->end_date,
        ]);
 
        if ($request->course_section_id) {
            $query->where('course_section_id', $request->course_section_id);
        }
 
        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }
 
        $statistics = [
            'total' => $query->count(),
            'present' => (clone $query)->where('status', 'present')->count(),
            'late' => (clone $query)->where('status', 'late')->count(),
            'absent' => (clone $query)->where('status', 'absent')->count(),
            'excused' => (clone $query)->where('status', 'excused')->count(),
        ];
 
        $statistics['attendance_rate'] = $statistics['total'] > 0
            ? round(($statistics['present'] + $statistics['late']) / $statistics['total'] * 100, 2)
            : 0;
 
        return response()->json($statistics);
    }
 
    /**
     * Bulk create attendances for a course section.
     */
    public function bulkCreate(Request $request)
    {
        $request->validate([
            'course_section_id' => 'required|exists:course_sections,id',
            'date' => 'required|date|before_or_equal:today',
            'teacher_id' => 'required|exists:teachers,id',
            'attendances' => 'required|array|min:1',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,late,absent,excused',
            'attendances.*.check_in_time' => 'nullable|date_format:H:i:s',
            'attendances.*.notes' => 'nullable|string|max:1000',
        ]);

        $created = [];
        $errors = [];
 
        foreach ($request->attendances as $attendanceData) {

            // Verificar si ya existe
            $exists = StudentAttendance::where('course_section_id', $request->course_section_id)
                ->where('student_id', $attendanceData['student_id'])
                ->where('date', $request->date)
                ->exists();
 
            if ($exists) {
                $student = Student::find($attendanceData['student_id']);

                $errors[] = [
                    'student_id' => $attendanceData['student_id'],
                    'message' => "Ya existe un registro de asistencia para {$student->full_name}.",
                ];
                continue;
            }
 
            $attendance = StudentAttendance::create([
                'course_section_id' => $request->course_section_id,
                'student_id' => $attendanceData['student_id'],
                'teacher_id' => $request->teacher_id,
                'date' => $request->date,
                'check_in_time' => $attendanceData['check_in_time'] ?? null,
                'status' => $attendanceData['status'],
                'notes' => $attendanceData['notes'] ?? null,
            ]);
 
            // Crear notificaciones automáticas si aplica
            if (in_array($attendance->status, ['late', 'absent'])) {
                $this->createNotificationsForAttendance($attendance);
            }
 
            $created[] = $attendance->id;
        }
 
        return response()->json([
            'message' => count($created) > 0
                ? sprintf('Se registraron %d asistencia(s) exitosamente.', count($created))
                : 'No se registró ninguna asistencia.',
            'created' => $created,
            'errors' => $errors,
        ], count($created) > 0 ? 201 : 422);
    }
 
    /**
     * Create notifications for guardians based on attendance.
     */
    private function createNotificationsForAttendance(StudentAttendance $attendance): void
    {
        $student = $attendance->student()->with('guardians')->first();
 
        if (!$student || $student->guardians->isEmpty()) {
            return;
        }
 
        $type = match($attendance->status) {
            'late' => 'late',
            'absent' => 'absent',
            'excused' => 'excused',
            default => null,
        };
 
        if (!$type) {
            return;
        }
 
        foreach ($student->guardians as $guardian) {
            // Determinar el método preferido
            $method = 'email';
            if (!empty($guardian->whatsapp_number)) {
                $method = 'whatsapp';
            } elseif (!empty($guardian->phone_number)) {
                $method = 'sms';
            }

            \App\Models\GuardianNotification::create([
                'attendance_id' => $attendance->id,
                'guardian_id' => $guardian->id,
                'type' => $type,
                'method' => $method,
                'status' => 'pending',
            ]);
        }
    }
}
