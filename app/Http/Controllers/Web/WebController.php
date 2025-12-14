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
            $documentsCount = Document::count();
            $categoriesCount = Category::count();
            $recentDocuments = Document::with(['category', 'user'])->latest()->take(5)->get();
        } else {
            $documentsCount = $user->documents()->count();
            $categoriesCount = $user->categories()->count();
            $recentDocuments = $user->documents()->with('category')->latest()->take(5)->get();
        }

        return view('dashboard', compact('documentsCount', 'categoriesCount', 'recentDocuments'));
    }

    public function documents()
    {
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
