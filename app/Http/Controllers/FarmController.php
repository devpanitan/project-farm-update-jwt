<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class FarmController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Apply the 'auth:api' middleware to all methods in this controller.
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Super Admins can see all farms.
        if ($user->can('isSuperAdmin')) {
            $farms = Farm::with('farmCategory')->latest()->get();
        } else {
            // Other users see only the farms they are members of.
            $farms = $user->farms()->with('farmCategory')->latest()->get();
        }

        return response()->json(['status' => 'success', 'data' => $farms]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Use the 'create' ability from FarmPolicy.
        $this->authorize('create', Farm::class);

        $validator = Validator::make($request->all(), [
            'farm_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'farm_category_id' => 'required|exists:farm_category,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $farm = Farm::create([
            'name' => $request->farm_name,
            'farm_cat_id' => $request->farm_category_id,
            'description' => $request->description,
        ]);

        // Attach the farm to the user who created it.
        $request->user()->farms()->attach($farm->id);
        
        // --- Start of Automatic Logging ---
        ActivityLog::create([
            'user_id'      => $request->user()->id,
            'action'       => 'created_farm',
            'subject_id'   => $farm->id,
            'subject_type' => get_class($farm),
            'description'  => "User '{$request->user()->username}' created farm '{$farm->name}'.",
            'after'        => $farm->toArray(),
        ]);
        // --- End of Automatic Logging ---

        return response()->json(['status' => 'success', 'message' => 'Farm created successfully.', 'data' => $farm->load('farmCategory')], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Farm $farm)
    {
        // Use the 'view' ability from FarmPolicy.
        $this->authorize('view', $farm);

        return response()->json(['status' => 'success', 'data' => $farm->load('farmCategory')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farm $farm)
    {
        // Use the 'update' ability from FarmPolicy.
        $this->authorize('update', $farm);

        $validator = Validator::make($request->all(), [
            'farm_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'farm_category_id' => 'sometimes|required|exists:farm_category,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // --- Logging: Capture state BEFORE update ---
        $beforeData = $farm->fresh()->toArray();

        $farm->update($request->all());

        // --- Start of Automatic Logging ---
        ActivityLog::create([
            'user_id'      => $request->user()->id,
            'action'       => 'updated_farm',
            'subject_id'   => $farm->id,
            'subject_type' => get_class($farm),
            'description'  => "User '{$request->user()->username}' updated farm '{$farm->name}'.",
            'before'       => $beforeData,
            'after'        => $farm->fresh()->toArray(),
        ]);
        // --- End of Automatic Logging ---

        return response()->json(['status' => 'success', 'message' => 'Farm updated successfully.', 'data' => $farm->load('farmCategory')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farm $farm)
    {
        // Use the 'delete' ability from FarmPolicy.
        $this->authorize('delete', $farm);

        // --- Logging: Capture state BEFORE deletion ---
        $beforeData = $farm->toArray();

        $farm->delete();

        // --- Start of Automatic Logging ---
        ActivityLog::create([
            'user_id'      => request()->user()->id,
            'action'       => 'deleted_farm',
            'subject_id'   => $beforeData['id'],
            'subject_type' => get_class($farm),
            'description'  => "User '" . request()->user()->username . "' deleted farm '{$beforeData['name']}'.",
            'before'       => $beforeData,
        ]);
        // --- End of Automatic Logging ---

        return response()->json(['status' => 'success', 'message' => 'Farm deleted successfully.']);
    }
}
