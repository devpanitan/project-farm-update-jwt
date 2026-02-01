<?php

namespace App\Http\Controllers;

use App\Models\ActuatorCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActuatorCommandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => ActuatorCommand::with('iotDevice')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|string|max:45|exists:iot_devices,uuid',
            'auto_rule_id' => 'nullable|integer|exists:auto_rules,id',
            'actuator_prefix' => 'nullable|string|max:50',
            'pin' => 'nullable|integer',
            'val' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $actuatorCommand = ActuatorCommand::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'data' => $actuatorCommand->load('iotDevice'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ActuatorCommand $actuatorCommand)
    {
        return response()->json([
            'status' => 'success',
            'data' => $actuatorCommand->load('iotDevice'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActuatorCommand $actuatorCommand)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'sometimes|required|string|max:45|exists:iot_devices,uuid',
            'auto_rule_id' => 'nullable|integer|exists:auto_rules,id',
            'actuator_prefix' => 'nullable|string|max:50',
            'pin' => 'nullable|integer',
            'val' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $actuatorCommand->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'data' => $actuatorCommand->load('iotDevice'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActuatorCommand $actuatorCommand)
    {
        $actuatorCommand->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'ActuatorCommand soft deleted successfully',
        ], 200);
    }
}
