<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuardianStudentRequest;
use App\Http\Requests\UpdateGuardianStudentRequest;
use App\Models\Guardian;
use App\Models\GuardianStudent;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuardianStudentController extends Controller
{
    /**
     * Display a listing of guardian-student relationships.
     */
    public function index(Request $request)
    {
        $query = GuardianStudent::query();

        // Filtro por guardian_id
        if ($guardianId = $request->input('guardian_id')) {
            $query->where('guardian_id', $guardianId);
        }

        // Filtro por student_id
        if ($studentId = $request->input('student_id')) {
            $query->where('student_id', $studentId);
        }

        // Filtro por tipo de relación
        if ($relationship = $request->input('relationship')) {
            $query->where('relationship', $relationship);
        }

        // Filtro por contacto primario
        if ($request->has('is_primary')) {
            $query->where('is_primary', $request->boolean('is_primary'));
        }

        $relationships = $query->latest('created_at')->paginate(15);

        return Inertia::render('GuardianStudent/Index', [
            'relationships' => $relationships,
            'filters' => $request->only(['guardian_id', 'student_id', 'relationship', 'is_primary']),
        ]);
    }

    /**
     * Show the form for creating a new guardian-student relationship.
     */
    public function create(): Response
    {
        $guardians = Guardian::with('user')->get();
        $students = Student::all();

        return Inertia::render('GuardianStudent/Create', [
            'guardians' => $guardians,
            'students' => $students,
        ]);
    }

    /**
     * Store a newly created guardian-student relationship.
     */
    public function store(StoreGuardianStudentRequest $request)
    {
        $validated = $request->validated();

        // Verificar si ya existe la relación
        $exists = GuardianStudent::where('guardian_id', $validated['guardian_id'])
            ->where('student_id', $validated['student_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'student_id' => 'Esta relación ya existe.'
            ]);
        }

        // Si se marca como primario, desmarcar otros primarios del mismo estudiante
        if ($validated['is_primary'] ?? false) {
            GuardianStudent::where('student_id', $validated['student_id'])
                ->update(['is_primary' => false]);
        }

        $relationship = GuardianStudent::create($validated);

        return redirect()->route('guardian-student.show', $relationship)
            ->with('success', 'Relación tutor-estudiante creada exitosamente.');
    }

    /**
     * Display the specified guardian-student relationship.
     */
    public function show(GuardianStudent $guardianStudent): Response
    {
        $guardianStudent->load(['guardian.user', 'student']);

        return Inertia::render('GuardianStudent/Show', [
            'relationship' => $guardianStudent,
        ]);
    }

    /**
     * Show the form for editing the specified guardian-student relationship.
     */
    public function edit(GuardianStudent $guardianStudent): Response
    {
        $guardianStudent->load(['guardian.user', 'student']);

        return Inertia::render('GuardianStudent/Edit', [
            'relationship' => $guardianStudent,
        ]);
    }

    /**
     * Update the specified guardian-student relationship.
     */
    public function update(UpdateGuardianStudentRequest $request, GuardianStudent $guardianStudent)
    {
        $validated = $request->validated();

        // Si se marca como primario, desmarcar otros primarios del mismo estudiante
        if (isset($validated['is_primary']) && $validated['is_primary']) {
            GuardianStudent::where('student_id', $guardianStudent->student_id)
                ->where('id', '!=', $guardianStudent->id)
                ->update(['is_primary' => false]);
        }

        $guardianStudent->update($validated);

        return redirect()->route('guardian-student.show', $guardianStudent)
            ->with('success', 'Relación tutor-estudiante actualizada exitosamente.');
    }

    /**
     * Remove the specified guardian-student relationship.
     */
    public function destroy(GuardianStudent $guardianStudent)
    {
        $guardianStudent->delete();

        return redirect()->route('guardian-student.index')
            ->with('success', 'Relación tutor-estudiante eliminada exitosamente.');
    }

    /**
     * Get relationships for a specific guardian.
     */
    public function byGuardian(Guardian $guardian)
    {
        $relationships = $guardian->students()
            ->withPivot(['relationship', 'is_primary', 'created_at'])
            ->get();

        return response()->json($relationships);
    }

    /**
     * Get relationships for a specific student.
     */
    public function byStudent(Student $student)
    {
        $relationships = $student->guardians()
            ->withPivot(['relationship', 'is_primary', 'created_at'])
            ->get();

        return response()->json($relationships);
    }

    /**
     * Set a guardian as primary contact for a student.
     */
    public function setPrimary(GuardianStudent $guardianStudent)
    {
        // Desmarcar todos los primarios del estudiante
        GuardianStudent::where('student_id', $guardianStudent->student_id)
            ->update(['is_primary' => false]);

        // Marcar este como primario
        $guardianStudent->update(['is_primary' => true]);

        return back()->with('success', 'Contacto primario actualizado exitosamente.');
    }
}
