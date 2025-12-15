<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Document;
use App\Models\AccessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebController extends Controller
{
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            $dependenciesCount = \App\Models\User::where('role', \App\Models\User::ROLE_DEPENDENCY)->count();
            $categoriesCount = Category::count();
            // Admin doesn't see documents list anymore
            $recentDocuments = collect(); // Empty collection

            return view('dashboard', compact('dependenciesCount', 'categoriesCount', 'recentDocuments'));
        } else {
            $documentsCount = $user->isDependency()
                ? $user->accessibleCategories()->withCount('documents')->get()->sum('documents_count')
                : $user->documents()->count();

            $categoriesCount = $user->categories()->count(); // Or accessible categories count

            // For dependency, fetch recent from accessible
            if ($user->isDependency()) {
                $categoryIds = $user->accessibleCategories()->pluck('categories.id')->toArray();
                $recentDocuments = Document::with(['category', 'user'])
                    ->whereIn('category_id', $categoryIds)
                    ->latest()
                    ->take(5)
                    ->get();
            } else {
                $recentDocuments = $user->documents()->with('category')->latest()->take(5)->get();
            }

            return view('dashboard', compact('documentsCount', 'categoriesCount', 'recentDocuments'));
        }
    }

    public function documents()
    {
        \Log::info('WebController@documents hit');
        
        if (!Auth::check()) {
            return response('ERROR: Session Lost. User ID is null. Session ID: ' . session()->getId(), 401);
        }

        \Log::info('User: ' . Auth::id());

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admins manage Categories and Dependencies, they don't view the documents feed generally.
            // But if they access this URL, let's redirect to Dashboard or Categories.
            return redirect()->route('dashboard');
        } elseif ($user->isDependency()) {
            // Dependency sees documents from categories they are assigned to
            // PLUS documents they have specifically requested and been approved for.

            // 1. Get IDs of categories assigned
            $categoryIds = $user->accessibleCategories()->pluck('categories.id')->toArray();

            // 2. Get IDs of documents approved via requests
            $requestedDocIds = \App\Models\AccessRequest::where('dependency_id', $user->id)
                ->where('status', 'approved')
                ->pluck('document_id')
                ->toArray();

            $documents = Document::with(['category', 'user'])
                ->where(function ($query) use ($categoryIds, $requestedDocIds) {
                    $query->whereIn('category_id', $categoryIds)
                        ->orWhereIn('id', $requestedDocIds);
                })
                ->latest()
                ->paginate(10);

        } else {
            // Citizen sees only their own documents
            $documents = $user->documents()->with('category')->latest()->paginate(10);
        }

        return view('documents.index', compact('documents'));
    }
}
