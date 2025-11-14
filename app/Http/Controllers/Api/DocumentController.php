<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/documents",
     *     tags={"Documentos"},
     *     summary="Listar todos los documentos del usuario",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filtrar por categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Buscar por título",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de documentos",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Document"))
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Document::where('user_id', $request->user()->id)
            ->with(['category', 'user']);

        // Filtrar por categoría
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Buscar por título
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        return response()->json($documents);
    }

    /**
     * @OA\Post(
     *     path="/api/documents",
     *     tags={"Documentos"},
     *     summary="Subir un nuevo documento",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title","file"},
     *                 @OA\Property(property="title", type="string", example="Contrato 2024"),
     *                 @OA\Property(property="description", type="string", example="Contrato de servicios"),
     *                 @OA\Property(property="file", type="string", format="binary"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="is_public", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Documento subido exitosamente"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'private');

        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_path' => $path,
            'category_id' => $request->category_id,
            'user_id' => $request->user()->id,
            'is_public' => $request->boolean('is_public', false),
        ]);

        return response()->json([
            'message' => 'Documento subido exitosamente',
            'document' => $document->load('category'),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/documents/{id}",
     *     tags={"Documentos"},
     *     summary="Obtener información de un documento",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Información del documento"),
     *     @OA\Response(response=403, description="No autorizado"),
     *     @OA\Response(response=404, description="Documento no encontrado")
     * )
     */
    public function show(Request $request, Document $document): JsonResponse
    {
        if (!$document->canAccess($request->user())) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $document->load(['category', 'user', 'sharedWith']);

        return response()->json($document);
    }

    /**
     * @OA\Get(
     *     path="/api/documents/{id}/download",
     *     tags={"Documentos"},
     *     summary="Descargar un documento",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Archivo descargado"),
     *     @OA\Response(response=403, description="No autorizado"),
     *     @OA\Response(response=404, description="Documento no encontrado")
     * )
     */
    public function download(Request $request, Document $document): StreamedResponse
    {
        if (!$document->canAccess($request->user())) {
            abort(403, 'No autorizado');
        }

        return Storage::disk('private')->download(
            $document->file_path,
            $document->original_filename
        );
    }

    /**
     * @OA\Put(
     *     path="/api/documents/{id}",
     *     tags={"Documentos"},
     *     summary="Actualizar información de un documento",
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
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="is_public", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Documento actualizado"),
     *     @OA\Response(response=403, description="No autorizado")
     * )
     */
    public function update(UpdateDocumentRequest $request, Document $document): JsonResponse
    {
        if ($document->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $document->update($request->validated());

        return response()->json([
            'message' => 'Documento actualizado exitosamente',
            'document' => $document->load('category'),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/documents/{id}",
     *     tags={"Documentos"},
     *     summary="Eliminar un documento",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Documento eliminado"),
     *     @OA\Response(response=403, description="No autorizado")
     * )
     */
    public function destroy(Request $request, Document $document): JsonResponse
    {
        if ($document->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Eliminar archivo físico
        Storage::disk('private')->delete($document->file_path);

        $document->delete();

        return response()->json([
            'message' => 'Documento eliminado exitosamente',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/documents/{id}/share",
     *     tags={"Documentos"},
     *     summary="Compartir un documento con otro usuario",
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
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer", example=2),
     *             @OA\Property(property="permission", type="string", enum={"view", "edit", "download"}, example="view")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Documento compartido"),
     *     @OA\Response(response=403, description="No autorizado")
     * )
     */
    public function share(Request $request, Document $document): JsonResponse
    {
        if ($document->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|in:view,edit,download',
        ]);

        $document->sharedWith()->syncWithoutDetaching([
            $request->user_id => ['permission' => $request->permission]
        ]);

        return response()->json([
            'message' => 'Documento compartido exitosamente',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/documents/shared",
     *     tags={"Documentos"},
     *     summary="Listar documentos compartidos conmigo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de documentos compartidos"
     *     )
     * )
     */
    public function shared(Request $request): JsonResponse
    {
        $documents = $request->user()
            ->sharedDocuments()
            ->with(['category', 'user'])
            ->get();

        return response()->json($documents);
    }
}
