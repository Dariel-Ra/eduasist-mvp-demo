<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::query();
 
        // Búsqueda por nombre, código o grado
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('enrollment_code', 'like', "%{$search}%")
                  ->orWhere('grade_level', 'like', "%{$search}%");
            });
        }

        // Filtro por grado
        if ($gradeLevel = $request->input('grade_level')) {
           $query->where('grade_level', $gradeLevel);
        }
 
        // Filtro por sección
        if ($section = $request->input('section')) {
            $query->where('section', $section);
        }
 
        // Filtro por estado activo
        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }
 
        $students = $query->latest('created_at')->paginate(15);
 
        return Inertia::render('Students/Index', [
            'students' => $students,
            'filters' => $request->only(['search', 'grade_level', 'section', 'active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Students/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        $student = Student::create($request->validated());

        return redirect()->route('students.show', $student)
            ->with('success', 'Estudiante creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): Response
    {
        $student->load(['guardians', 'sections', 'attendances']);

        return Inertia::render('Students/Show', [
            'student' => $student,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student): Response
    {
        return Inertia::render('Students/Edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());

        return redirect()->route('students.show', $student)
            ->with('success', 'Estudiante actualizado exitosamente.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
 
        return redirect()->route('students.index')
            ->with('success', 'Estudiante eliminado exitosamente.');
    }
 
    /**
     * Get all grade levels for filtering.
     */
    public function gradeLevels()
    {
        $gradeLevels = Student::whereNotNull('grade_level')
            ->distinct()
            ->pluck('grade_level')
            ->sort()
            ->values();
            
        return response()->json($gradeLevels);
    }
 
    /**
     * Get all sections for filtering.
     */
    public function sections()
    {
        $sections = Student::whereNotNull('section')
            ->distinct()
            ->pluck('section')
            ->sort()
            ->values();
 
        return response()->json($sections);
    }
}
