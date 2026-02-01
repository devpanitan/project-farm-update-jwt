<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('userRole')->latest()->get();
        return response()->json(['status' => 'success', 'data' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|max:255|unique:users,email',
            'user_role_id' => 'required|exists:user_roles,id',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'tel' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'google' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        // The 'password' is already hashed automatically by the User model's $casts attribute.

        $user = User::create($data);

        return response()->json(['status' => 'success', 'message' => 'User created successfully.', 'data' => $user->load('userRole')], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with('userRole')->find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'username' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8', // Optional: only update if provided
            'user_role_id' => 'sometimes|required|exists:user_roles,id',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'tel' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // The User model will automatically hash the password if it is present in the data array.
        $user->update($data);

        return response()->json(['status' => 'success', 'message' => 'User updated successfully.', 'data' => $user->load('userRole')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'User deleted successfully.']);
    }
}
