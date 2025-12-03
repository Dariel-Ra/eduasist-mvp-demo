<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = User::query();

 

        // Search filter

        if ($request->filled('search')) {

            $search = $request->input('search');

            $query->where(function ($q) use ($search) {

                $q->where('first_name', 'like', "%{$search}%")

                  ->orWhere('last_name', 'like', "%{$search}%")

                  ->orWhere('email', 'like', "%{$search}%")

                  ->orWhere('phone', 'like', "%{$search}%");

            });

        }

 

        // Role filter

        if ($request->filled('role')) {

            $query->where('role', $request->input('role'));

        }

 

        // Status filter

        if ($request->filled('status')) {

            $query->where('status', $request->input('status'));

        }

 

        // Sorting

        $sortBy = $request->input('sort_by', 'created_at');

        $sortOrder = $request->input('sort_order', 'desc');

 

        switch ($sortBy) {

            case 'name':

                $query->orderBy('first_name', $sortOrder)

                      ->orderBy('last_name', $sortOrder);

                break;

            case 'email':

                $query->orderBy('email', $sortOrder);

                break;

            case 'created_at':

            default:

                $query->orderBy('created_at', $sortOrder);

                break;

        }

 

        $users = $query->paginate(10)->withQueryString();

 

        return Inertia::render('users/index', [

            'users' => $users,

            'filters' => [

                'search' => $request->input('search'),

                'role' => $request->input('role'),

                'status' => $request->input('status'),

                'sort_by' => $sortBy,

                'sort_order' => $sortOrder,

            ],

            'roles' => ['sysadmin', 'admin', 'teacher', 'guardian'],

            'statuses' => ['active', 'inactive'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('users/create', [
            'availableRoles' => $this->getAvailableRoles($request->user()),
            'statuses' => ['active', 'inactive'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // Verificar que el usuario puede crear este rol
        $availableRoles = $this->getAvailableRoles($request->user());
 
        if (!in_array($request->role, $availableRoles)) {
            abort(403, 'No tienes permisos para crear usuarios con este rol.');
        }
 
        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status ?? 'active',
            'password' => $request->password,
        ]);
 
        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): Response
    {
        return Inertia::render('users/show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request,User $user): Response
    {
        return Inertia::render('users/edit', [
            'user' => $user,
            'availableRoles' => $this->getAvailableRoles($request->user()),
            'statuses' => ['active', 'inactive'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // Verificar que el usuario puede asignar este rol
        $availableRoles = $this->getAvailableRoles($request->user());
 
        if (!in_array($request->role, $availableRoles)) {
            abort(403, 'No tienes permisos para asignar este rol.');
        }
 
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => $request->status,
        ];
 
        // Solo actualizar password si se proporcionó
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }
 
        $user->update($data);
 
        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevenir que el usuario se elimine a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar tu propia cuenta desde aquí.');
        }
 
        $user->delete();
 
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
 
    /**
     * Get available roles based on the authenticated user's role.
     */
    private function getAvailableRoles(User $user): array
    {
        return match ($user->role) {
            'sysadmin' => ['admin', 'teacher', 'guardian'],
            'admin' => ['teacher', 'guardian'],
            default => [],
        };
    }
}
