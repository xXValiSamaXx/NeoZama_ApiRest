<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Enforce check for all methods
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin()) {
                abort(403, 'Acceso restringido a administradores.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Admins can see all categories, or maybe just global ones + their own?
        // Let's show all for admin to manage master data.
        $categories = Category::orderBy('created_at', 'desc')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = new Category($validated);
        // If an admin creates a category, it could be global (user_id = null) or personal.
        // For "Master Data" usually it's global. Let's make it global by default for admins?
        // Or keep it personal? The prompt said "maestro-detalle", so user->categories.
        // But "Panel de control" usually implies system-wide management.
        // Let's stick to the previous logic but allow admins to manage everything.
        // If admin creates it, let's assign it to correct user or null. 
        // For simplicity, let's keep assigning to Auth::id() for now, or null if checkbox "Global".
        // Let's just save it.
        $category->user_id = Auth::id(); // Admin owns it
        $category->save();

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada exitosamente.');
    }
}
