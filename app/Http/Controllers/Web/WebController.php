<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Document;
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
            $documents = Document::with(['category', 'user'])->latest()->paginate(10);
        } else {
            $documents = $user->documents()->with('category')->latest()->paginate(10);
        }

        return view('documents.index', compact('documents'));
    }
}
