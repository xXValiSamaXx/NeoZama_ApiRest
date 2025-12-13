<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\Document;
use App\Models\DocumentShare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessRequestController extends Controller
{
    /**
     * Dependency creates a request to view a document.
     */
    public function store(Request $request)
    {
        // Only valid if user is a Dependency
        if (!Auth::user()->isDependency()) {
            return response()->json(['message' => 'Solo las dependencias pueden solicitar acceso.'], 403);
        }

        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'reason' => 'required|string',
            'valid_until' => 'nullable|date|after:now',
        ]);

        $document = Document::findOrFail($validated['document_id']);

        // Check if already shared
        $alreadyShared = DocumentShare::where('document_id', $document->id)
            ->where('shared_with_user_id', Auth::id())
            ->exists();

        if ($alreadyShared) {
            return response()->json(['message' => 'Ya tienes acceso a este documento.'], 400);
        }

        // Check if pending request exists
        $pending = AccessRequest::where('dependency_id', Auth::id())
            ->where('document_id', $document->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            return response()->json(['message' => 'Ya existe una solicitud pendiente para este documento.'], 400);
        }

        $accessRequest = AccessRequest::create([
            'dependency_id' => Auth::id(),
            'user_id' => $document->user_id, // Owner
            'document_id' => $document->id,
            'reason' => $validated['reason'],
            'valid_until' => $validated['valid_until'] ?? now()->addDays(7), // Default validity
        ]);

        return response()->json([
            'message' => 'Solicitud de acceso enviada correctamente.',
            'request' => $accessRequest
        ], 201);
    }

    /**
     * Admin approves or rejects a request.
     */
    public function update(Request $request, AccessRequest $accessRequest)
    {
        // Only Admin can update
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado. Solo administrador puede aprobar.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $accessRequest->update([
            'status' => $validated['status'],
            'approved_at' => $validated['status'] === 'approved' ? now() : null,
        ]);

        if ($validated['status'] === 'approved') {
            DocumentShare::firstOrCreate(
                [
                    'document_id' => $accessRequest->document_id,
                    'shared_with_user_id' => $accessRequest->dependency_id,
                ],
                [
                    'permission' => 'view' // Strict default
                ]
            );
        }

        return response()->json([
            'message' => 'Solicitud ' . ($validated['status'] === 'approved' ? 'aprobada' : 'rechazada') . '.',
            'request' => $accessRequest
        ]);
    }

    /**
     * List requests for the user (received) or dependency (sent).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isDependency()) {
            $requests = $user->accessRequestsSent()->with('document')->get();
        } else {
            $requests = $user->accessRequestsReceived()->with(['document', 'dependency'])->get();
        }

        return response()->json($requests);
    }
}
