<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessRequestController extends Controller
{
    /**
     * List access requests.
     * For Dependencies: List sent requests.
     * For Users: List received requests.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isDependency()) {
            $requests = $user->accessRequestsSent()->with('document')->orderBy('created_at', 'desc')->get();
            return view('access_requests.dependency_index', compact('requests'));
        } elseif ($user->isAdmin()) {
            // Admin sees ALL pending requests (or all requests)
            $requests = AccessRequest::where('status', 'pending')
                ->with(['document', 'dependency', 'user']) // Eager load user (citizen) too
                ->orderBy('created_at', 'desc')
                ->get();
            return view('access_requests.admin_index', compact('requests'));
        } else {
            // Citizens don't handle requests.
            abort(403, 'Acceso no autorizado.');
        }
    }

    /**
     * Show form for Dependency to request access (optional, maybe they search docs first).
     * For now, a simple form where they input document ID (proof of concept) or list all public/searchable docs.
     * Let's assume there's a "Search" page, but for now we create a standalone request page.
     */
    public function create()
    {
        if (!Auth::user()->isDependency()) {
            abort(403, 'Solo dependencias pueden solicitar acceso.');
        }
        // In a real app, this would be a search. For prototype, listing all docs might be too much.
        // Let's pass an empty list or expected search behavior.
        // For simplicity: We will skip a complex search UI and assume they Request from a known ID or simple list.
        $documents = Document::orderBy('created_at', 'desc')->take(20)->get();
        return view('access_requests.create', compact('documents'));
    }

    /**
     * Store request from Dependency.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isDependency()) {
            abort(403);
        }

        $validated = $request->validate([
            'document_id' => 'required|exists:documents,id',
            'reason' => 'required|string',
        ]);

        // Logic similar to API...
        $document = Document::findOrFail($validated['document_id']);

        // Check dups... (omitted for brevity, handled in API typically, putting basics here)
        AccessRequest::create([
            'dependency_id' => Auth::id(),
            'user_id' => $document->user_id,
            'document_id' => $document->id,
            'reason' => $validated['reason'],
            'status' => 'pending',
            'valid_until' => now()->addDays(7),
        ]);

        return redirect()->route('web.access-requests.index')->with('success', 'Solicitud enviada.');
    }

    /**
     * Admin approves/rejects request.
     */
    public function update(Request $request, AccessRequest $accessRequest)
    {
        // Only Admin can update
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo el administrador puede aprobar solicitudes.');
        }

        $status = $request->input('status'); // 'approved' or 'rejected'

        if (in_array($status, ['approved', 'rejected'])) {
            $accessRequest->update([
                'status' => $status,
                'approved_at' => $status === 'approved' ? now() : null,
            ]);

            if ($status === 'approved') {
                // Create Share logic duplication (service class would be better but direct here for speed)
                \App\Models\DocumentShare::firstOrCreate(
                    [
                        'document_id' => $accessRequest->document_id,
                        'shared_with_user_id' => $accessRequest->dependency_id,
                    ],
                    ['permission' => 'view']
                );
            }
        }

        return redirect()->route('web.access-requests.index')->with('success', 'Solicitud actualizada.');
    }

    /**
     * Show Secure View-Only page for Dependency.
     */
    public function secureView(Document $document)
    {
        $user = Auth::user();
        if ($user->id !== $document->user_id) {
            // Check share
            $shared = \App\Models\DocumentShare::where('document_id', $document->id)
                ->where('shared_with_user_id', $user->id)
                ->exists();
            if (!$shared)
                abort(403);
        }

        // Log access
        \App\Models\DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'action' => 'viewed_web',
            'accessed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('documents.secure_view', compact('document'));
    }
    /**
     * Stream the file content for the previewer (Iframe/Object).
     */
    public function streamFile(Document $document)
    {
        $user = Auth::user();

        // Permission Check (Same as secureView)
        // Creator always can view
        $isCreator = $user->id === $document->user_id;

        // Admin always can view (if logical, otherwise restrict)
        $isAdmin = $user->isAdmin();

        // Dependency check via Share
        $isShared = \App\Models\DocumentShare::where('document_id', $document->id)
            ->where('shared_with_user_id', $user->id)
            ->exists();

        if (!$isCreator && !$isAdmin && !$isShared) {
            abort(403);
        }

        // Return file
        $path = $document->file_path; // Assuming 'file_path' is the column relative to storage
        // Ensure path exists in storage
        if (!\Illuminate\Support\Facades\Storage::exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        // Log 'download/stream' action
        \App\Models\DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => $user->id,
            'action' => 'streamed_web',
            'accessed_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return \Illuminate\Support\Facades\Storage::response($path);
    }
}
