<?php

namespace App\Http\Controllers;

use App\Models\CourseSection;
use App\Models\Course;
use App\Models\Teacher;
use App\Enums\ScheduleDay;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\StoreCourseSectionRequest;
use App\Http\Requests\UpdateCourseSectionRequest;

class CourseSectionController extends Controller
{
    /**
     * Display a listing of the course sections.
     */
    public function index(Request $request)
    {
        $query = CourseSection::with(['course', 'teacher.user']);

        // Búsqueda por sección, aula o curso
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('section', 'like', "%{$search}%")
                  ->orWhere('classroom', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teacher.user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
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

        // Filtro por estado activo/inactivo
        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $sections = $query->latest('created_at')->paginate(15);

        return Inertia::render('course-sections/index', [
            'sections' => $sections,
            'filters' => $request->only(['search', 'course_id', 'teacher_id', 'active']),
        ]);
    }

    /**
     * Show the form for creating a new course section.
     */
    public function create(): Response
    {
        $courses = Course::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $teachers = Teacher::with('user:id,name')
            ->get()
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->user->name,
                    'code' => $teacher->code,
                ];
            });

        return Inertia::render('course-sections/create', [
            'courses' => $courses,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Store a newly created course section in storage.
     */
    public function store(StoreCourseSectionRequest $request)
    {
        $section = CourseSection::create($request->validated());

        return redirect()->route('course-sections.show', $section)
            ->with('success', 'Sección de curso creada exitosamente.');
    }

    /**
     * Display the specified course section.
     */
    public function show(CourseSection $courseSection): Response
    {
        $courseSection->load([
            'course',
            'teacher.user',
            'students' => function ($query) {
                $query->withPivot('status')->limit(10);
            }
        ]);

        // Contar estudiantes inscritos
        $studentsCount = $courseSection->students()->count();

        return Inertia::render('course-sections/show', [
            'section' => $courseSection,
            'studentsCount' => $studentsCount,
        ]);
    }

    /**
     * Show the form for editing the specified course section.
     */
    public function edit(CourseSection $courseSection): Response
    {
        $courseSection->load(['course', 'teacher.user']);

        $courses = Course::where('active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $teachers = Teacher::with('user:id,name')
            ->get()
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->user->name,
                    'code' => $teacher->code,
                ];
            });

        return Inertia::render('course-sections/edit', [
            'section' => $courseSection,
            'courses' => $courses,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Update the specified course section in storage.
     */
    public function update(UpdateCourseSectionRequest $request, CourseSection $courseSection)
    {
        $courseSection->update($request->validated());

        return redirect()->route('course-sections.show', $courseSection)
            ->with('success', 'Sección de curso actualizada exitosamente.');
    }

    /**
     * Remove the specified course section from storage.
     */
    public function destroy(CourseSection $courseSection)
    {
        // Verificar si tiene estudiantes inscritos
        if ($courseSection->students()->count() > 0) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar una sección con estudiantes inscritos.'
            ]);
        }

        $courseSection->delete();

        return redirect()->route('course-sections.index')
            ->with('success', 'Sección de curso eliminada exitosamente.');
    }

    /**
     * Get sections by course for filtering.
     */
    public function byCourse(Course $course)
    {
        $sections = $course->sections()
            ->with('teacher.user')
            ->where('active', true)
            ->get();

        return response()->json($sections);
    }

    /**
     * Get sections by teacher for filtering.
     */
    public function byTeacher(Teacher $teacher)
    {
        $sections = $teacher->sections()
            ->with('course')
            ->where('active', true)
            ->get();

        return response()->json($sections);
    }

    /**
     * Get available days for a section.
     */
    public function availableDays()
    {
        return response()->json(ScheduleDay::toArray());
    }
}
