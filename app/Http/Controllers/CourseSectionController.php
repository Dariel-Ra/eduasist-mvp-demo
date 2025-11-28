<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\Course;
use App\Models\Teacher;
use App\Http\Requests\StoreCourseSectionRequest;
use App\Http\Requests\UpdateCourseSectionRequest;
use App\Http\Resources\CourseSectionResource;
use App\Enums\ScheduleDay;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CourseSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CourseSection::with(['course', 'teacher']);
 
        // Búsqueda por sección, aula o nombre del curso
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('section', 'like', "%{$search}%")
                  ->orWhere('classroom', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teacher', function ($q) use ($search) {
                      $q->whereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                  });
            });
        }
 
        // Filtro por curso
        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }
 
        // Filtro por profesor
        if ($teacherId = $request->input('teacher_id')) {
            $query->where('teacher_id', $teacherId);
        }

        // Filtro por día de la semana
        if ($day = $request->input('day')) {
            $query->withScheduleOn($day);
        }
 
        // Filtro por estado activo/inactivo
        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }
 
        // Filtro por disponibilidad de cupos
        if ($request->boolean('has_available_seats')) {
            $query->whereRaw('(max_students IS NULL OR max_students > (SELECT COUNT(*) FROM course_section_student WHERE course_section_id = course_sections.id))');
        }
 
        // Ordenamiento
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
 
        $query->orderBy($sortBy, $sortOrder);
 
        $courseSections = $query
            ->withCount('students')
            ->paginate(15)
            ->withQueryString();
 
        return Inertia::render('CourseSections/Index', [
            'course_sections' => CourseSectionResource::collection($courseSections),
            'filters' => $request->only([
                'search',
                'course_id',
                'teacher_id',
                'day',
                'active',
                'has_available_seats',
                'sort_by',
                'sort_order'
            ]),
            'schedule_days' => ScheduleDay::toOptions(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $courses = Course::where('active', true)
            ->orderBy('name')
            ->get();
 
        $teachers = Teacher::with('user')
            ->whereHas('user', function ($q) {
                $q->where('active', true);
            })
            ->get();
 
        return Inertia::render('CourseSections/Create', [
            'courses' => $courses,
            'teachers' => $teachers,
            'schedule_days' => ScheduleDay::toOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseSectionRequest $request)
    {
        $courseSection = CourseSection::create($request->validated());

        return redirect()->route('course-sections.show', $courseSection)
            ->with('success', 'Sección del curso creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseSection $courseSection)
    {
        $courseSection->load([
            'course',
            'teacher.user',
            'students' => function ($query) {
                $query->orderBy('first_name')->orderBy('last_name');
            },
        ]);
 
        return Inertia::render('CourseSections/Show', [
            'course_section' => CourseSectionResource::make($courseSection),
            'schedule_days' => ScheduleDay::toOptions(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseSection $courseSection)
    {
        $courseSection->load(['course', 'teacher']);
 
        $courses = Course::where('active', true)
            ->orderBy('name')
            ->get();
 
        $teachers = Teacher::with('user')
            ->whereHas('user', function ($q) {
                $q->where('active', true);
            })
            ->get();
 
        return Inertia::render('CourseSections/Edit', [
            'course_section' => CourseSectionResource::make($courseSection),
            'courses' => $courses,
            'teachers' => $teachers,
            'schedule_days' => ScheduleDay::toOptions(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseSectionRequest $request, CourseSection $courseSection)
    {
        $courseSection->update($request->validated());
 
        return redirect()->route('course-sections.show', $courseSection)
            ->with('success', 'Sección del curso actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseSection $courseSection)
    {
        // Verificar si hay estudiantes inscritos
        if ($courseSection->students()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una sección con estudiantes inscritos.');
        }
 
        $courseSection->delete();
 
        return redirect()->route('course-sections.index')
            ->with('success', 'Sección del curso eliminada exitosamente.');
    }

    /**
     * Get all active course sections for a specific course.
     */
    public function byCourse(Course $course)
    {
        $sections = $course->sections()
            ->active()
            ->with(['teacher.user'])
            ->withCount('students')
            ->get();
 
        return response()->json([
            'data' => CourseSectionResource::collection($sections),
        ]);
    }
 
    /**
     * Get all active course sections for a specific teacher.
     */
    public function byTeacher(Teacher $teacher)
    {
        $sections = CourseSection::byTeacher($teacher->id)
            ->active()
            ->with(['course'])
            ->withCount('students')
            ->get();
 
        return response()->json([
            'data' => CourseSectionResource::collection($sections),
        ]);
    }
 
    /**
     * Get sections with classes on a specific day.
     */
    public function byDay(Request $request)
    {
        $request->validate([
            'day' => 'required|in:' . implode(',', ScheduleDay::values()),
        ]);
 
        $sections = CourseSection::withScheduleOn($request->day)
            ->active()
            ->with(['course', 'teacher.user'])
            ->withCount('students')
            ->get();
 
        return response()->json([
            'data' => CourseSectionResource::collection($sections),
        ]);
    }
 
    /**
     * Get sections currently in session.
     */
    public function inSession()
    {
        $sections = CourseSection::active()
            ->with(['course', 'teacher.user'])
            ->get()
            ->filter(fn ($section) => $section->isInSession());
 
        return response()->json([
            'data' => CourseSectionResource::collection($sections),
        ]);
    }
 
    /**
     * Enroll a student in a course section.
     */
    public function enrollStudent(Request $request, CourseSection $courseSection)
    {
        $request->validate([
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],
        ]);
 
        // Verificar si hay cupos disponibles
        if (!$courseSection->hasAvailableSeats()) {
            return back()->with('error', 'La sección no tiene cupos disponibles.');
        }
 
        // Verificar si el estudiante ya está inscrito
        if ($courseSection->students()->where('student_id', $request->student_id)->exists()) {
            return back()->with('error', 'El estudiante ya está inscrito en esta sección.');
        }
 
        $courseSection->students()->attach($request->student_id, [
            'status' => 'active'
        ]);
 
        return back()->with('success', 'Estudiante inscrito exitosamente.');
    }
 
    /**
     * Remove a student from a course section.
     */
    public function unenrollStudent(Request $request, CourseSection $courseSection)
    {
        $request->validate([
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],
        ]);
 
        $courseSection->students()->detach($request->student_id);
 
        return back()->with('success', 'Estudiante retirado exitosamente.');
    }
 
    /**
     * Update student status in a course section.
     */
    public function updateStudentStatus(Request $request, CourseSection $courseSection)
    {
        $request->validate([
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],
            'status' => [
                'required',
                'in:active,dropped',
            ],
        ]);
 
        $courseSection->students()->updateExistingPivot($request->student_id, [
            'status' => $request->status,
        ]);
 
        return back()->with('success', 'Estado del estudiante actualizado exitosamente.');
    }
}
