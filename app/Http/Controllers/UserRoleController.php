<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');

        $this->middleware(function ($request, $next) {
            if (!Gate::allows('isSuperAdmin')) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        })->except(['index', 'show']); // Allow anyone authenticated to view roles
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = UserRole::latest()->get();
        return response()->json(['status' => 'success', 'data' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255|unique:user_roles,role_name',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $role = UserRole::create($validator->validated());

        return response()->json(['status' => 'success', 'message' => 'User role created successfully.', 'data' => $role], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $userRole = UserRole::find($id);
        if (!$userRole) {
            return response()->json(['status' => 'error', 'message' => 'User role not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $userRole]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // It's generally a bad idea to allow modification of core roles like Super Admin.
        // We will prevent editing of the first 3 roles (Super Admin, Owner, Worker)
        if ($id <= 3) {
             return response()->json(['status' => 'error', 'message' => 'Updating core system roles is not permitted.'], 403);
        }

        $userRole = UserRole::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'role_name' => 'sometimes|required|string|max:255|unique:user_roles,role_name,' . $userRole->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $userRole->update($validator->validated());

        return response()->json(['status' => 'success', 'message' => 'User role updated successfully.', 'data' => $userRole]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Prevent deletion of core roles
        if ($id <= 3) {
             return response()->json(['status' => 'error', 'message' => 'Deleting core system roles is not permitted.'], 403);
        }

        $userRole = UserRole::findOrFail($id);
        $userRole->delete();
        return response()->json(['status' => 'success', 'message' => 'User role deleted successfully.']);
    }
}
