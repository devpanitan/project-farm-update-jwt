<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // This is the most important line. It connects the controller to the UserPolicy.
        // It automatically calls the policy methods for the corresponding controller actions.
        // For example, an incoming request to the 'index' method will call the 'viewAny' policy method.
        // 'show' calls 'view', 'store' calls 'create', 'update' calls 'update', etc.
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * This action is protected by the 'viewAny' method in UserPolicy.
     * By default, only Super Admins will be able to access this.
     */
    public function index()
    {
        return response()->json(User::latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * This action is protected by the 'create' method in UserPolicy.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username'      => 'required|string|unique:users,username',
            'password'      => 'required|string',
            'email'         => 'nullable|email|unique:users,email',
            'user_role_id'  => 'nullable|integer|exists:user_roles,id',
            'google'        => 'nullable|string',
            'tel'           => 'nullable|string|max:20',
            'address'       => 'nullable|string',
            'birth_date'    => 'nullable|date',
            'firstname'     => 'nullable|string|max:255',
            'lastname'      => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        $newUser = User::create($validated);

        return response()->json(['status' => 'User created successfully', 'user' => $newUser], 201);
    }

    /**
     * Display the specified resource.
     *
     * This action is protected by the 'view' method in UserPolicy.
     * A user can only view their own profile, unless they are a Super Admin.
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * This action is protected by the 'update' method in UserPolicy.
     * A user can only update their own profile, unless they are a Super Admin.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username'      => 'sometimes|required|string|unique:users,username,'.$user->id,
            'email'         => 'sometimes|nullable|email|unique:users,email,'.$user->id,
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

        return response()->json(['status' => 'User updated successfully', 'user' => $user->makeHidden(['password'])]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * This action is protected by the 'delete' method in UserPolicy.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['status' => 'User deleted successfully']);
    }

    /**
     * Display all farms associated with a specific user.
     * This is a custom method, so we need to authorize it manually.
     */
    public function showFarms(string $id)
    {
        $user = User::with('farms')->findOrFail($id);
        
        // Manually authorize that the currently authenticated user can 'view' the $user model.
        // This uses the same logic from UserPolicy@view.
        $this->authorize('view', $user);

        return response()->json(['status' => 'success', 'data' => $user]);
    }
}
