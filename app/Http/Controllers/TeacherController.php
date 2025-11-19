<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController extends Controller
{
    /**
     * Display a listing of the teachers.
     */
    public function index(Request $request)
    {
        $query = Teacher::with('user');

        // Búsqueda por nombre de usuario, código o especialidad
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('specialty', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por especialidad
        if ($specialty = $request->input('specialty')) {
            $query->where('specialty', $specialty);
        }

        $teachers = $query->latest('created_at')->paginate(15);

        return Inertia::render('Teachers/Index', [
            'teachers' => $teachers,
            'filters' => $request->only(['search', 'specialty']),
        ]);
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create(): Response
    {
        return Inertia::render('Teachers/Create');
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(StoreTeacherRequest $request)
    {
        $teacher = Teacher::create($request->validated());

        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Profesor creado exitosamente.');
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher): Response
    {
        $teacher->load('user');

        return Inertia::render('Teachers/Show', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher): Response
    {
        $teacher->load('user');

        return Inertia::render('Teachers/Edit', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $teacher->update($request->validated());

        return redirect()->route('teachers.show', $teacher)
            ->with('success', 'Profesor actualizado exitosamente.');
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return redirect()->route('teachers.index')
            ->with('success', 'Profesor eliminado exitosamente.');
    }

    /**
     * Get all specialties for filtering.
     */
    public function specialties()
    {
        $specialties = Teacher::whereNotNull('specialty')
            ->distinct()
            ->pluck('specialty')
            ->sort()
            ->values();

        return response()->json($specialties);
    }
}
