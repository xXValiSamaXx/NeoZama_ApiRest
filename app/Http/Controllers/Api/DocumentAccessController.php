<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentAccessLog;
use App\Models\DocumentShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentAccessController extends Controller
{
    /**
     * Special view endpoint for dependencies that logs access.
     */
    public function show(Request $request, Document $document)
    {
        $user = Auth::user();

        // Check if user has access via share table
        $hasAccess = DocumentShare::where('document_id', $document->id)
            ->where('shared_with_user_id', $user->id)
            ->exists();

        // Owner also has access
        $isOwner = $document->user_id === $user->id;

        if (!$hasAccess && !$isOwner) {
            return response()->json(['message' => 'No tienes permiso para ver este documento.'], 403);
        }

        // LOGGING (Audit) logic
        // We log every access here.
        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'action' => 'viewed',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'accessed_at' => now(),
        ]);

        return response()->json([
            'document' => $document,
            'url' => asset('storage/' . $document->file_path), // Or secure signed URL
            'audit_logged' => true
        ]);
    }
}
