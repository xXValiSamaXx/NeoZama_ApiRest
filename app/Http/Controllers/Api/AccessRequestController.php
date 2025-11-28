<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Models\Category;
use App\Models\DependencyPermission;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessRequestController extends Controller
{
    /**
     * List access requests.
     * If user is admin, show all (or filtered).
     * If user belongs to dependency, show their requests.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = AccessRequest::with(['dependency', 'user', 'resource']);

        if (!$user->is_admin) {
            // If regular user, only show requests from their dependency
            if ($user->dependency_id) {
                $query->where('dependency_id', $user->dependency_id);
            } else {
                // Users without dependency shouldn't see requests?
                return response()->json([], 200);
            }
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        return $query->latest()->paginate();
    }

    /**
     * Create a new access request.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->dependency_id) {
            return response()->json(['message' => 'User does not belong to any dependency.'], 403);
        }

        $validated = $request->validate([
            'resource_type' => 'required|in:document,category',
            'resource_id' => 'required|integer',
            'justification' => 'required|string',
        ]);

        $resourceClass = $validated['resource_type'] === 'document' ? Document::class : Category::class;

        // Validate resource existence
        if (!$resourceClass::where('id', $validated['resource_id'])->exists()) {
             return response()->json(['message' => 'Resource not found.'], 404);
        }

        // Check if already requested (pending) or approved
        $exists = AccessRequest::where('dependency_id', $user->dependency_id)
            ->where('resource_type', $resourceClass)
            ->where('resource_id', $validated['resource_id'])
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Access already requested or granted.'], 409);
        }

        $accessRequest = AccessRequest::create([
            'dependency_id' => $user->dependency_id,
            'user_id' => $user->id,
            'resource_type' => $resourceClass,
            'resource_id' => $validated['resource_id'],
            'status' => 'pending',
            'justification' => $validated['justification'],
        ]);

        return response()->json($accessRequest, 201);
    }

    /**
     * Approve or Reject a request (Admin only).
     */
    public function updateStatus(Request $request, AccessRequest $accessRequest)
    {
        // TODO: Ensure admin policy
        // For now check is_admin
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($accessRequest->status !== 'pending') {
            return response()->json(['message' => 'Request is not pending.'], 400);
        }

        DB::transaction(function () use ($accessRequest, $validated, $request) {
            $accessRequest->update([
                'status' => $validated['status'],
                'authorized_by' => $request->user()->id,
                'authorized_at' => now(),
            ]);

            if ($validated['status'] === 'approved') {
                // Create permission
                DependencyPermission::create([
                    'dependency_id' => $accessRequest->dependency_id,
                    'permissionable_type' => $accessRequest->resource_type,
                    'permissionable_id' => $accessRequest->resource_id,
                ]);
            }
        });

        return response()->json($accessRequest);
    }
}
