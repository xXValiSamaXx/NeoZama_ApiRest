<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DependencyController extends Controller
{
    // Constructor removed - access control handled via Route Middleware/Gates

    /**
     * Display a listing of dependency users.
     */
    public function index()
    {
        $dependencies = User::where('role', User::ROLE_DEPENDENCY)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dependencies.index', compact('dependencies'));
    }

    /**
     * Show the form for creating a new dependency.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.dependencies.create', compact('categories'));
    }

    /**
     * Store a newly created dependency in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_DEPENDENCY,
        ]);

        if (isset($validated['categories'])) {
            $user->accessibleCategories()->sync($validated['categories']);
        }

        return redirect()->route('admin.dependencies.index')
            ->with('success', 'Dependencia creada exitosamente.');
    }

    /**
     * Show the form for editing the specified dependency.
     */
    public function edit(User $dependency)
    {
        // Ensure we are editing a dependency
        if (!$dependency->isDependency()) {
            abort(404);
        }

        $categories = Category::all();
        $assignedCategories = $dependency->accessibleCategories->pluck('id')->toArray();

        return view('admin.dependencies.edit', compact('dependency', 'categories', 'assignedCategories'));
    }

    /**
     * Update the specified dependency in storage.
     */
    public function update(Request $request, User $dependency)
    {
        if (!$dependency->isDependency()) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $dependency->id,
            'password' => 'nullable|string|min:8|confirmed',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $dependency->name = $validated['name'];
        $dependency->email = $validated['email'];

        if (!empty($validated['password'])) {
            $dependency->password = Hash::make($validated['password']);
        }

        $dependency->save();

        // Sync Categories
        $dependency->accessibleCategories()->sync($request->input('categories', []));

        return redirect()->route('admin.dependencies.index')
            ->with('success', 'Dependencia actualizada exitosamente.');
    }

    /**
     * Remove the specified dependency from storage.
     */
    public function destroy(User $dependency)
    {
        if (!$dependency->isDependency()) {
            abort(403, 'Solo se pueden eliminar cuentas de dependencia.');
        }

        $dependency->delete();

        return redirect()->route('admin.dependencies.index')
            ->with('success', 'Dependencia eliminada exitosamente.');
    }
}
