<?php

namespace App\Http\Controllers;

use App\Models\UserFarm;
use App\Models\Farm;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UserFarmController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     * Only accessible by Super Admins.
     */
    public function index(Request $request)
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }
        return response()->json(['status' => 'success', 'data' => UserFarm::with(['user', 'farm'])->get()]);
    }

    /**
     * Store a newly created resource in storage.
     * Assign a user to a farm.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'farm_id' => 'required|exists:farms,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $farm = Farm::findOrFail($request->farm_id);
        $userToAdd = User::findOrFail($request->user_id);

        // Authorize: Only farm owner or super admin can add members.
        // We can reuse the 'update' policy for the Farm model which is restricted to owners.
        $this->authorize('update', $farm);

        // Check for uniqueness
        $existing = UserFarm::where('user_id', $request->user_id)->where('farm_id', $request->farm_id)->first();
        if ($existing) {
            return response()->json(['status' => 'error', 'message' => 'This user is already a member of the farm.'], 409);
        }

        $userFarm = UserFarm::create($request->all());

        // --- Start of Automatic Logging ---
        ActivityLog::create([
            'user_id'      => $request->user()->id,
            'action'       => 'added_user_to_farm',
            'subject_id'   => $userFarm->id,
            'subject_type' => get_class($userFarm),
            'description'  => "User '{$request->user()->username}' added user '{$userToAdd->username}' to farm '{$farm->name}'.",
            'after'        => $userFarm->load(['user', 'farm'])->toArray(),
        ]);
        // --- End of Automatic Logging ---

        return response()->json(['status' => 'success', 'message' => 'User added to farm successfully.', 'data' => $userFarm], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, UserFarm $userFarm)
    {
        // Authorize: User must be a member of the farm to view the relationship.
        $this->authorize('view', $userFarm->farm);

        return response()->json(['status' => 'success', 'data' => $userFarm->load(['user', 'farm'])]);
    }

    /**
     * Update is generally not a standard operation for a pivot table.
     * Restricted to Super Admins for now.
     */
    public function update(Request $request, UserFarm $userFarm)
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'This action is restricted to Super Admins.'], 403);
        }
        
        // The logic for updating is complex and potentially dangerous.
        // It's better to delete and create a new relationship.
        return response()->json(['status' => 'error', 'message' => 'Updating a UserFarm relationship is not supported. Please delete and create a new one.'], 405); // 405 Method Not Allowed
    }

    /**
     * Remove the specified resource from storage.
     * Remove a user from a farm.
     */
    public function destroy(Request $request, UserFarm $userFarm)
    {
        $user = $request->user();

        // Authorize: 
        // 1. A Super Admin can remove anyone.
        // 2. The Farm Owner can remove anyone from their farm.
        // 3. A user can remove themselves (leave the farm).
        $isOwner = $user->isOwnerOfFarm($userFarm->farm_id);
        $isSelf = $user->id === $userFarm->user_id;

        if ($user->isSuperAdmin() || $isOwner || $isSelf) {
            
            // --- Logging: Capture state BEFORE deletion ---
            $beforeData = $userFarm->load(['user', 'farm'])->toArray();
            $removedUserName = $beforeData['user']['username'] ?? 'N/A';
            $farmName = $beforeData['farm']['name'] ?? 'N/A';

            $userFarm->delete();

            // --- Start of Automatic Logging ---
            ActivityLog::create([
                'user_id'      => $request->user()->id,
                'action'       => 'removed_user_from_farm',
                'subject_id'   => $beforeData['id'],
                'subject_type' => get_class($userFarm),
                'description'  => "User '{$request->user()->username}' removed user '{$removedUserName}' from farm '{$farmName}'.",
                'before'       => $beforeData,
            ]);
            // --- End of Automatic Logging ---

            return response()->json(['status' => 'success', 'message' => 'User removed from farm successfully.']);
        } 

        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
    }
}
