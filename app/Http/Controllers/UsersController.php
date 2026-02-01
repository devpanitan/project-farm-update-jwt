<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'error' => 'Unauthorized'
            ], 401);
        }

        return response()->json($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation rules now match the database schema (nullable fields)
        $validated = $request->validate([
            'username'      => 'required|string|unique:users,username',
            'password'      => 'required|string',
            'email'         => 'nullable|email|unique:users,email',
            'user_role_id'  => 'nullable|integer|exists:user_roles,id', // Validates that the role ID exists
            'google'        => 'nullable|string',
            'tel'           => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'birth_date'    => 'nullable|date',
            'firstname'     => 'nullable|string|max:255',
            'lastname'      => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        // Use create() method which is safer and respects $fillable
        $newUser = User::create($validated);

        return response()->json(['status' => 'User created successfully', 'user' => $newUser], 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'username'      => 'sometimes|required|string|unique:users,username,'.$id,
            'email'         => 'sometimes|nullable|email|unique:users,email,'.$id,
            'user_role_id'  => 'sometimes|nullable|integer|exists:user_roles,id',
            'google'        => 'sometimes|nullable|string',
            'tel'           => 'sometimes|nullable|string|max:20',
            'address'       => 'sometimes|nullable|string',
            'birth_date'    => 'sometimes|nullable|date',
            'firstname'     => 'sometimes|nullable|string|max:255',
            'lastname'      => 'sometimes|nullable|string|max:255',
        ]);

        if ($request->has('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return response()->json(['status' => 'User updated successfully', 'user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete(); // This performs a soft delete because of the SoftDeletes trait in the User model

        return response()->json(['status' => 'User deleted successfully']);
    }
}
