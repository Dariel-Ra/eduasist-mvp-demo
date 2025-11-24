<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index(Request $request)
    {
        $query = Course::query();

        // Búsqueda por nombre, código o nivel de grado
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('grade_level', 'like', "%{$search}%");
            });
        }

        // Filtro por nivel de grado
        if ($gradeLevel = $request->input('grade_level')) {
            $query->where('grade_level', $gradeLevel);
        }

        // Filtro por estado activo/inactivo
        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $courses = $query->latest('created_at')->paginate(15);

        return Inertia::render('courses/index', [
            'courses' => $courses,
            'filters' => $request->only(['search', 'grade_level', 'active']),
        ]);
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): Response
    {
        return Inertia::render('courses/create');
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());

        return redirect()->route('courses.show', $course)
            ->with('success', 'Curso creado exitosamente.');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): Response
    {
        $course->load('sections');

        return Inertia::render('courses/show', [
            'course' => $course,
        ]);
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): Response
    {
        return Inertia::render('courses/edit', [
            'course' => $course,
        ]);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());

        return redirect()->route('courses.show', $course)
            ->with('success', 'Curso actualizado exitosamente.');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Curso eliminado exitosamente.');
    }

    /**
     * Get all grade levels for filtering.
     */
    public function gradeLevels()
    {
        $gradeLevels = Course::whereNotNull('grade_level')
            ->distinct()
            ->pluck('grade_level')
            ->sort()
            ->values();

        return response()->json($gradeLevels);
    }
}
