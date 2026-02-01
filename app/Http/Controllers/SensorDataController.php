<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use App\Models\IotDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SensorDataController extends Controller
{
    /**
     * Display a listing of the resource.
     * Allows filtering by uuid.
     */
    public function index(Request $request)
    {
        $query = SensorData::with(['iotDevice', 'sensorType']);

        if ($request->has('uuid')) {
            $query->where('uuid', $request->input('uuid'));
        }

        $sensorData = $query->latest()->get();
        return response()->json(['status' => 'success', 'data' => $sensorData]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => ['required', 'string', 'max:45', Rule::exists('iot_devices', 'uuid')],
            'sensor_prefix' => 'nullable|string|max:50',
            'val' => 'required|numeric',
            'sensor_type_id' => ['required', 'integer', Rule::exists('sensor_types', 'id')],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        
        // Optional: Automatically fetch sensor_prefix from the IotDevice if not provided
        if (empty($validatedData['sensor_prefix'])) {
            $device = IotDevice::where('uuid', $validatedData['uuid'])->first();
            if ($device) {
                $validatedData['sensor_prefix'] = $device->sensor_prefix;
            }
        }

        $sensorData = SensorData::create($validatedData);

        return response()->json(['status' => 'success', 'message' => 'Sensor data logged successfully.', 'data' => $sensorData->load(['iotDevice', 'sensorType'])], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sensorData = SensorData::with(['iotDevice', 'sensorType'])->find($id);
        if (!$sensorData) {
            return response()->json(['status' => 'error', 'message' => 'Sensor data not found.'], 404);
        }
        return response()->json(['status' => 'success', 'data' => $sensorData]);
    }

    /**
     * Update is typically not supported for historical sensor data.
     */
    public function update(Request $request, $id)
    {
        // Generally, sensor logs should be immutable. If an update is truly needed,
        // the logic would be similar to the store method.
        // For this project, we will forbid updates.
        return response()->json(['status' => 'error', 'message' => 'Update operation is not supported for sensor data.'], 405);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sensorData = SensorData::find($id);
        if (!$sensorData) {
            return response()->json(['status' => 'error', 'message' => 'Sensor data not found.'], 404);
        }
        $sensorData->delete();
        return response()->json(['status' => 'success', 'message' => 'Sensor data deleted successfully.']);
    }
}
