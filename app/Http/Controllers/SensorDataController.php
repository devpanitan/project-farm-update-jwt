<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use App\Models\IotDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SensorDataController extends Controller
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
     * Filters by uuid if provided, but respects user authorization.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', SensorData::class);

        $user = $request->user();

        if ($user->isSuperAdmin()) {
            // Super Admin can see all sensor data.
            $query = SensorData::with(['iotDevice.farm', 'sensorType']);
             // Optional: Allow admin to filter by a specific device uuid if provided
            if ($request->has('uuid')) {
                $query->where('uuid', $request->input('uuid'));
            }
        } else {
            // Regular users only see data from their farms.
            $farmIds = $user->farms()->pluck('farms.id');
            $deviceUuids = IotDevice::whereIn('farm_id', $farmIds)->pluck('uuid');
            
            $query = SensorData::with(['iotDevice.farm', 'sensorType'])->whereIn('uuid', $deviceUuids);

            // Further filter by a specific device uuid if requested and authorized for that user
            if ($request->has('uuid')) {
                $requestedUuid = $request->input('uuid');
                if ($deviceUuids->contains($requestedUuid)) {
                    $query->where('uuid', $requestedUuid);
                }
            }
        }

        $sensorData = $query->latest()->paginate(100);
        return response()->json(['status' => 'success', 'data' => $sensorData]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', SensorData::class);

        $validator = Validator::make($request->all(), [
            'uuid' => ['required', 'string', 'max:45', Rule::exists('iot_devices', 'uuid')],
            'val' => 'required|numeric',
            'sensor_type_id' => ['required', 'integer', Rule::exists('sensor_types', 'id')],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // Check if the user is authorized for the specific IoT device
        $device = IotDevice::where('uuid', $request->uuid)->firstOrFail();
        $this->authorize('update', $device); // Creating sensor data is like updating the device state.

        $validatedData = $validator->validated();
        $validatedData['sensor_prefix'] = $device->sensor_prefix; // Auto-assign prefix from device

        $sensorData = SensorData::create($validatedData);

        return response()->json(['status' => 'success', 'message' => 'Sensor data logged successfully.', 'data' => $sensorData->load(['iotDevice', 'sensorType'])], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SensorData $sensorData)
    {
        $this->authorize('view', $sensorData);

        return response()->json(['status' => 'success', 'data' => $sensorData->load(['iotDevice.farm', 'sensorType'])]);
    }

    /**
     * Update is not supported for historical sensor data.
     */
    public function update(Request $request, SensorData $sensorData)
    {
        $this->authorize('update', $sensorData);
        // Policy will prevent this from being reached by non-Admins.
        return response()->json(['status' => 'error', 'message' => 'Update operation is not supported for sensor data.'], 405);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SensorData $sensorData)
    {
        $this->authorize('delete', $sensorData);

        $sensorData->delete();
        return response()->json(['status' => 'success', 'message' => 'Sensor data deleted successfully.']);
    }
}
