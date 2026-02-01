<?php

namespace App\Http\Controllers;

use App\Models\IotDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IotDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = IotDevice::with('farm');

        if ($request->has('farm_id')) {
            $query->where('farm_id', $request->input('farm_id'));
        }

        $devices = $query->latest()->get();
        return response()->json(['status' => 'success', 'data' => $devices]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')],
            'description' => 'nullable|string',
            'status' => 'boolean',
            'unit' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // UUID is generated automatically by the model event
        $device = IotDevice::create($validator->validated());
        return response()->json(['status' => 'success', 'message' => 'IoT device created successfully.', 'data' => $device->load('farm')], 201);
    }

    /**
     * Display the specified resource.
     * Can be found by either id or uuid.
     */
    public function show($id)
    {
        $device = IotDevice::with('farm')
            ->where('id', $id)
            ->orWhere('uuid', $id)
            ->first();

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'IoT device not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $device]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $device = IotDevice::where('id', $id)->orWhere('uuid', $id)->first();
        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'IoT device not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'farm_id' => ['sometimes', 'required', 'integer', Rule::exists('farms', 'id')],
            'description' => 'nullable|string',
            'status' => 'sometimes|boolean',
            'unit' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $device->update($validator->validated());
        return response()->json(['status' => 'success', 'message' => 'IoT device updated successfully.', 'data' => $device->load('farm')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $device = IotDevice::where('id', $id)->orWhere('uuid', $id)->first();
        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'IoT device not found.'], 404);
        }
        $device->delete();
        return response()->json(['status' => 'success', 'message' => 'IoT device deleted successfully.']);
    }
}
