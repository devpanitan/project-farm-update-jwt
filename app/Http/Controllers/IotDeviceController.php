<?php

namespace App\Http\Controllers;

use App\Models\IotDevice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Farm;

class IotDeviceController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource for the user's farms.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $this->authorize('viewAny', IotDevice::class);

        // Instead of allowing any farm_id, we get devices from the farms the user is a member of.
        $farmIds = $user->farms()->pluck('id');
        $devices = IotDevice::with('farm')->whereIn('farm_id', $farmIds)->latest()->get();

        return response()->json(['status' => 'success', 'data' => $devices]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', IotDevice::class);

        $validator = Validator::make($request->all(), [
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')],
            'description' => 'nullable|string',
            'status' => 'boolean',
            'unit' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // Additionally, check if the user can create a device in this specific farm.
        $farm = Farm::findOrFail($request->farm_id);
        $this->authorize('update', $farm); // Creating a device is like updating a farm.

        $device = IotDevice::create($validator->validated());

        // --- Start of Automatic Logging ---
        ActivityLog::create([
            'user_id'      => $request->user()->id,
            'action'       => 'created_iot_device',
            'subject_id'   => $device->id,
            'subject_type' => get_class($device),
            'description'  => "User '{$request->user()->username}' created a new device.",
            'after'        => $device->toArray(),
        ]);
        // --- End of Automatic Logging ---

        return response()->json(['status' => 'success', 'message' => 'IoT device created successfully.', 'data' => $device->load('farm')], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(IotDevice $iotDevice)
    {
        $this->authorize('view', $iotDevice);

        return response()->json(['status' => 'success', 'data' => $iotDevice->load('farm')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IotDevice $iotDevice)
    {
        $this->authorize('update', $iotDevice);

        $validator = Validator::make($request->all(), [
            'farm_id' => ['sometimes', 'required', 'integer', Rule::exists('farms', 'id')],
            'description' => 'nullable|string',
            'status' => 'sometimes|boolean',
            'unit' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $iotDevice->update($validator->validated());
        return response()->json(['status' => 'success', 'message' => 'IoT device updated successfully.', 'data' => $iotDevice->load('farm')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IotDevice $iotDevice)
    {
        $this->authorize('delete', $iotDevice);

        // --- Logging: Capture state BEFORE deletion ---
        $beforeData = $iotDevice->toArray();

        $iotDevice->delete();

        // --- Start of Automatic Logging ---
        ActivityLog::create([
            'user_id'      => request()->user()->id,
            'action'       => 'deleted_iot_device',
            'subject_id'   => $beforeData['id'],
            'subject_type' => get_class($iotDevice),
            'description'  => "User '" . request()->user()->username . "' deleted device with UUID '{$beforeData['uuid']}'.",
            'before'       => $beforeData,
        ]);
        // --- End of Automatic Logging ---

        return response()->json(['status' => 'success', 'message' => 'IoT device deleted successfully.']);
    }
}
