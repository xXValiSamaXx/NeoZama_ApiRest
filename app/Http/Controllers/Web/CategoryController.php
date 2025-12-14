<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            \Log::info('CategoryController@index: Iniciando...');
            \Log::info('Usuario autenticado: ' . auth()->user()->email);

            // Admins and Dependencies can see categories.
            $categories = Category::orderBy('created_at', 'desc')->get();
            \Log::info('Categorías cargadas: ' . $categories->count());

            \Log::info('Intentando cargar vista...');
            return view('categories.index', compact('categories'));
        } catch (\Exception $e) {
            \Log::error('ERROR en CategoryController@index: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            // Mostrar error al usuario
            return response()->view('errors.generic', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado.');
        }
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = new Category($validated);
        $category->user_id = Auth::id(); // Admin owns it
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Categoría creada con éxito.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado.');
        }
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acceso denegado.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
