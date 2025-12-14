<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"Categorías"},
     *     summary="Listar todas las categorías del usuario",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorías",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Category"))
     *     )
     * )
     */
    /**
     * Listar categorías (Operación READ del CRUD).
     * 
     * REQUISITO: "Relación Maestro-Detalle".
     * Aquí obtenemos las categorías (Maestro) y contamos sus documentos (Detalle).
     * 
     * Categorías son globales, todos los usuarios pueden verlas.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            // Admin sees everything
            $categories = Category::withCount('documents')->orderBy('created_at', 'desc')->get();
        } elseif ($user->isDependency()) {
            // Dependencies only see what they are assigned to (for auditing/viewing)
            $categories = $user->accessibleCategories()->withCount('documents')->get();
        } else {
            // Regular Users (Citizens) need to see categories to upload documents.
            // Usually they upload to any "Public/Global" category or categories available to them.
            // For now, return all categories so they can choose.
            // TODO: If we need to hide internal categories from Citizens, add a 'is_public' flag later.
            $categories = Category::withCount('documents')->orderBy('created_at', 'desc')->get();
        }

        return response()->json($categories);
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     tags={"Categorías"},
     *     summary="Crear nueva categoría",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Facturas"),
     *             @OA\Property(property="description", type="string", example="Facturas del año 2024")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Categoría creada"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        // Solo el admin puede crear categorías
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Categoría creada exitosamente',
            'category' => $category,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     tags={"Categorías"},
     *     summary="Obtener una categoría específica",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Detalles de la categoría"),
     *     @OA\Response(response=404, description="Categoría no encontrada")
     * )
     */
    public function show(Request $request, Category $category): JsonResponse
    {
        $category->load('documents');

        return response()->json($category);
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     tags={"Categorías"},
     *     summary="Actualizar una categoría",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Categoría actualizada"),
     *     @OA\Response(response=403, description="No autorizado")
     * )
     */
    public function update(StoreCategoryRequest $request, Category $category): JsonResponse
    {
        // Solo el admin puede actualizar categorías
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $category->update($request->validated());

        return response()->json([
            'message' => 'Categoría actualizada exitosamente',
            'category' => $category,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     tags={"Categorías"},
     *     summary="Eliminar una categoría",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Categoría eliminada"),
     *     @OA\Response(response=403, description="No autorizado")
     * )
     */
    public function destroy(Request $request, Category $category): JsonResponse
    {
        // Solo el admin puede eliminar categorías
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente',
        ]);
    }
}
