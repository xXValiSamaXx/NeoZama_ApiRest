<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        if (!Auth::check()) {
            return response('ERROR: Session Lost during show. User ID is null. Session ID: ' . session()->getId(), 401);
        }

        \Log::info('DocumentController@show hit for document: ' . $document->id);
        \Log::info('User: ' . Auth::id());
        
        $this->authorizeAccess($document);

        if (!Storage::disk('public')->exists($document->file_path)) {
            \Log::error('File not found: ' . $document->file_path);
            abort(404, 'El archivo no existe.');
        }

        return response()->file(storage_path('app/public/' . $document->file_path));
    }

    /**
     * Download the specified resource.
     */
    public function download(Document $document)
    {
        $this->authorizeAccess($document);

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'El archivo no existe.');
        }

        return Storage::disk('public')->download($document->file_path, $document->original_filename);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        if (Auth::id() !== $document->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'No tienes permiso para eliminar este documento.');
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Documento eliminado correctamente.');
    }

    /**
     * Check if user can access the document
     */
    private function authorizeAccess(Document $document)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return true;
        }

        if ($document->user_id === $user->id) {
            return true;
        }

        if ($user->isDependency()) {
            // Check if dependency has access to the category or specific request
            $hasCategoryAccess = $user->accessibleCategories()->where('categories.id', $document->category_id)->exists();
            if ($hasCategoryAccess) {
                return true;
            }
            
            // Check specific request
            // This logic might need to be expanded based on AccessRequest model, but for now basic check
             $hasRequest = \App\Models\AccessRequest::where('dependency_id', $user->id)
                ->where('document_id', $document->id)
                ->where('status', 'approved')
                ->exists();
            
            if ($hasRequest) {
                return true;
            }
        }

        abort(403, 'No tienes permiso para acceder a este documento.');
    }
}
