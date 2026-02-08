<?php

namespace App\Http\Controllers;

use App\Models\ActuatorCommand;
use App\Models\IotDevice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ActuatorCommandController extends Controller
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
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ActuatorCommand::class);

        $user = $request->user();
        $farmIds = $user->farms()->pluck('id');
        $deviceUuids = IotDevice::whereIn('farm_id', $farmIds)->pluck('uuid');

        $query = ActuatorCommand::with(['iotDevice.farm'])->whereIn('uuid', $deviceUuids);

        $commands = $query->latest()->paginate(50);

        return response()->json(['status' => 'success', 'data' => $commands]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', ActuatorCommand::class);

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

        $device = IotDevice::where('uuid', $request->uuid)->firstOrFail();
        $this->authorize('update', $device); // Issuing a command is an update-like action on the device.

        $actuatorCommand = ActuatorCommand::create($validator->validated());

        // --- Start of Automatic Logging ---
        ActivityLog::create([
            'user_id'      => $request->user()->id,
            'action'       => 'created_actuator_command',
            'subject_id'   => $actuatorCommand->id,
            'subject_type' => get_class($actuatorCommand),
            'description'  => "User '{$request->user()->username}' created a command for device '{$device->uuid}'.",
            'after'        => $actuatorCommand->toArray()
        ]);
        // --- End of Automatic Logging ---

        return response()->json([
            'status' => 'success',
            'message' => 'Actuator command created successfully.',
            'data' => $actuatorCommand->load('iotDevice'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ActuatorCommand $actuatorCommand)
    {
        $this->authorize('view', $actuatorCommand);

        return response()->json([
            'status' => 'success',
            'data' => $actuatorCommand->load('iotDevice.farm'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActuatorCommand $actuatorCommand)
    {
        $this->authorize('update', $actuatorCommand);

        return response()->json([
            'status' => 'error',
            'message' => 'Update operation is not supported for actuator commands.'
        ], 405);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActuatorCommand $actuatorCommand)
    {
        $this->authorize('delete', $actuatorCommand);
        
        // Policy prevents this, but as a safeguard:
        return response()->json([
            'status' => 'error',
            'message' => 'Delete operation is not supported for actuator commands.'
        ], 405);
    }
}
