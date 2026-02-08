<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityLogRequest;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Manually check if the user is a super admin
        if (!$request->user() || !$request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        // Retrieve all activity logs, newest first, with pagination
        $activityLogs = ActivityLog::latest()->paginate(20);

        return response()->json($activityLogs);
    }

    /**
     * Store a newly created activity log in storage.
     */
    public function store(StoreActivityLogRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $activityLog = ActivityLog::create($validatedData);

        return response()->json($activityLog, 201);
    }
}
