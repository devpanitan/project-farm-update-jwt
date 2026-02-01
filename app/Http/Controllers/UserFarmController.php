<?php

namespace App\Http\Controllers;

use App\Models\UserFarm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserFarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['status' => 'success', 'data' => UserFarm::with(['user', 'farm'])->get()]);
    }

    /**
     * Store a newly created resource in storage.
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

        // Check for uniqueness
        $existing = UserFarm::where('user_id', $request->user_id)->where('farm_id', $request->farm_id)->first();
        if ($existing) {
            return response()->json(['status' => 'error', 'message' => 'Duplicate entry'], 409);
        }

        $userFarm = UserFarm::create($request->all());

        return response()->json(['status' => 'success', 'message' => 'UserFarm created successfully.', 'data' => $userFarm], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserFarm $userFarm)
    {
        return response()->json(['status' => 'success', 'data' => $userFarm->load(['user', 'farm'])]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserFarm $userFarm)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'farm_id' => 'sometimes|required|exists:farms,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
        
        // Check for uniqueness before update
        if ($request->has('user_id') && $request->has('farm_id')) {
            $existing = UserFarm::where('user_id', $request->user_id)->where('farm_id', $request->farm_id)->where('id', '!=', $userFarm->id)->first();
            if ($existing) {
                return response()->json(['status' => 'error', 'message' => 'Duplicate entry'], 409);
            }
        }

        $userFarm->update($request->all());

        return response()->json(['status' => 'success', 'message' => 'UserFarm updated successfully.', 'data' => $userFarm]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserFarm $userFarm)
    {
        $userFarm->delete();
        return response()->json(['status' => 'success', 'message' => 'UserFarm deleted successfully.']);
    }
}
