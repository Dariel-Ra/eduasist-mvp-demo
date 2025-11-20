<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use Illuminate\Http\Request;
use App\Http\Requests\StoreGuardianRequest;
use App\Http\Requests\UpdateGuardianRequest;
use Inertia\Inertia;
use Inertia\Response;

class GuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Guardian::with('user');

        // Búsqueda por nombre de usuario, email o teléfono
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('personal_email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
 
        $guardians = $query->latest('created_at')->paginate(15);
 
        return Inertia::render('Guardians/Index', [
            'guardians' => $guardians,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new guardian.
     */
    public function create(): Response
    {
        return Inertia::render('Guardians/Create');
    }
 
    /**
     * Store a newly created guardian in storage.
     */
    public function store(StoreGuardianRequest $request)
    {
        $guardian = Guardian::create($request->validated());
 
        return redirect()->route('guardians.show', $guardian)
            ->with('success', 'Tutor creado exitosamente.');
    }
 
    /**
     * Display the specified guardian.
     */
    public function show(Guardian $guardian): Response
    {
        $guardian->load(['user', 'students']);
 
        return Inertia::render('Guardians/Show', [
            'guardian' => $guardian,
        ]);
    }
 
    /**
     * Show the form for editing the specified guardian.
     */
    public function edit(Guardian $guardian): Response
    {
        $guardian->load('user');
 
        return Inertia::render('Guardians/Edit', [
            'guardian' => $guardian,
        ]);
    }
 
    /**
     * Update the specified guardian in storage.
     */
    public function update(UpdateGuardianRequest $request, Guardian $guardian)
    {
        $guardian->update($request->validated());
 
        return redirect()->route('guardians.show', $guardian)
            ->with('success', 'Tutor actualizado exitosamente.');
    }
 
    /**
     * Remove the specified guardian from storage.
     */
    public function destroy(Guardian $guardian)
    {
        $guardian->delete();
 
        return redirect()->route('guardians.index')
            ->with('success', 'Tutor eliminado exitosamente.');
    }
}
