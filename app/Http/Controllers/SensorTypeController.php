<?php

namespace App\Http\Controllers;

use App\Models\SensorType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SensorTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sensorTypes = SensorType::latest()->get();
        return response()->json(['status' => 'success', 'data' => $sensorTypes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_name' => 'required|string|max:100|unique:sensor_types,type_name',
            'unit' => 'required|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $sensorType = SensorType::create($validator->validated());

        return response()->json(['status' => 'success', 'message' => 'Sensor type created successfully.', 'data' => $sensorType], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sensorType = SensorType::find($id);
        if (!$sensorType) {
            return response()->json(['status' => 'error', 'message' => 'Sensor type not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $sensorType]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $sensorType = SensorType::find($id);
        if (!$sensorType) {
            return response()->json(['status' => 'error', 'message' => 'Sensor type not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'type_name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('sensor_types')->ignore($sensorType->id)],
            'unit' => 'sometimes|required|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $sensorType->update($validator->validated());

        return response()->json(['status' => 'success', 'message' => 'Sensor type updated successfully.', 'data' => $sensorType]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sensorType = SensorType::find($id);
        if (!$sensorType) {
            return response()->json(['status' => 'error', 'message' => 'Sensor type not found.'], 404);
        }
        $sensorType->delete();
        return response()->json(['status' => 'success', 'message' => 'Sensor type deleted successfully.']);
    }
}
