<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller
{
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
        $userRole = UserRole::find($id);
        if (!$userRole) {
            return response()->json(['status' => 'error', 'message' => 'User role not found.'], 404);
        }

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
        $userRole = UserRole::find($id);
        if (!$userRole) {
            return response()->json(['status' => 'error', 'message' => 'User role not found.'], 404);
        }
        $userRole->delete();
        return response()->json(['status' => 'success', 'message' => 'User role deleted successfully.']);
    }
}
