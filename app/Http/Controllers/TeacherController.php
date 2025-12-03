<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;

class TeacherController extends Controller
{
    /**
     * Display a listing of the teachers.
     */
    public function index(Request $request)
    {
        $query = Teacher::with('user');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('specialty', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Specialty filter
        if ($request->filled('specialty')) {
            $query->where('specialty', $request->input('specialty'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        switch ($sortBy) {
            case 'name':
                $query->join('users', 'teachers.user_id', '=', 'users.id')
                      ->orderBy('users.first_name', $sortOrder)
                      ->orderBy('users.last_name', $sortOrder)
                      ->select('teachers.*');
                break;
            case 'code':
                $query->orderBy('code', $sortOrder);
                break;
            case 'specialty':
                $query->orderBy('specialty', $sortOrder);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }

        $teachers = $query->paginate(10)->withQueryString();

        // Get all unique specialties for filter
        $specialties = Teacher::whereNotNull('specialty')
            ->distinct()
            ->pluck('specialty')
            ->sort()
            ->values();

        return Inertia::render('teachers/index', [
            'teachers' => $teachers,
            'filters' => [
                'search' => $request->input('search'),
                'specialty' => $request->input('specialty'),
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
            ],
            'specialties' => $specialties,
        ]);
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function create(): Response
    {
        // Get users with role=teacher that don't have a teacher profile yet
        $availableUsers = \App\Models\User::where('role', 'teacher')
            ->whereDoesntHave('teacher')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => "{$user->first_name} {$user->last_name}",
                    'email' => $user->email,
                ];
            });

        return Inertia::render('teachers/create', [
            'availableUsers' => $availableUsers,
        ]);
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
        $teacher->load('user', 'sections', 'attendances');

        return Inertia::render('teachers/show', [
            'teacher' => $teacher,
        ]);
    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(Teacher $teacher): Response
    {
        $teacher->load('user');

        return Inertia::render('teachers/edit', [
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