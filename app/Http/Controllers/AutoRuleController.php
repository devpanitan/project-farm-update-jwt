<?php

namespace App\Http\Controllers;

use App\Models\AutoRule;
use App\Models\ActuatorCommand;
use App\Models\IotDevice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AutoRuleController extends Controller
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
        $this->authorize('viewAny', AutoRule::class);
        
        $user = $request->user();
        $farmIds = $user->farms()->pluck('id');
        $deviceIds = IotDevice::whereIn('farm_id', $farmIds)->pluck('id');

        $rules = AutoRule::with(['iotDevice', 'actuator'])
            ->whereIn('iot_device_id', $deviceIds)
            ->latest()
            ->paginate(50);

        return response()->json(['status' => 'success', 'data' => $rules]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', AutoRule::class);

        $validator = Validator::make($request->all(), [
            'iot_device_id' => 'required|exists:iot_devices,id',
            'actuator_id' => [
                'required',
                'exists:actuator_commands,id',
                function ($attribute, $value, $fail) {
                    $actuator = ActuatorCommand::find($value);
                    if ($actuator && $actuator->auto_rule_id !== null) {
                        $fail('The selected actuator is already assigned to another auto rule.');
                    }
                },
            ],
            'description' => 'nullable|string|max:255',
            'activate_interval' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        $device = IotDevice::findOrFail($validatedData['iot_device_id']);
        $this->authorize('update', $device);

        $autoRule = null; // Initialize to null

        try {
            DB::beginTransaction();

            $autoRule = AutoRule::create($validatedData);
            $actuator = ActuatorCommand::find($validatedData['actuator_id']);
            $actuator->auto_rule_id = $autoRule->id;
            $actuator->save();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during the transaction.',
                'error_details' => $e->getMessage()
            ], 500);
        }

        // --- Start of Automatic Logging (after successful transaction) ---
        if ($autoRule) {
            ActivityLog::create([
                'user_id'      => $request->user()->id,
                'action'       => 'created_auto_rule',
                'subject_id'   => $autoRule->id,
                'subject_type' => get_class($autoRule),
                'description'  => "User '{$request->user()->username}' created a new auto rule: '{$autoRule->description}'.",
                'after'        => $autoRule->load('iotDevice', 'actuator')->toArray(),
            ]);
        }
        // --- End of Automatic Logging ---

        return response()->json([
            'status' => 'success',
            'data' => $autoRule->load('iotDevice', 'actuator'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AutoRule $autoRule)
    {
        $this->authorize('view', $autoRule);
        return response()->json([
            'status' => 'success',
            'data' => $autoRule->load('iotDevice', 'actuator'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AutoRule $autoRule)
    {
        $this->authorize('update', $autoRule);

        $validator = Validator::make($request->all(), [
            'iot_device_id' => 'sometimes|required|exists:iot_devices,id',
            'actuator_id' => [
                'sometimes',
                'required',
                'exists:actuator_commands,id',
                function ($attribute, $value, $fail) use ($autoRule) {
                    $actuator = ActuatorCommand::find($value);
                    if ($actuator && $actuator->auto_rule_id !== null && $actuator->auto_rule_id !== $autoRule->id) {
                        $fail('The selected actuator is already assigned to another auto rule.');
                    }
                },
            ],
            'description' => 'nullable|string|max:255',
            'activate_interval' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $validatedData = $validator->validated();
        $beforeData = $autoRule->fresh()->load('iotDevice', 'actuator')->toArray();

        try {
            DB::beginTransaction();

            $originalActuatorId = $autoRule->actuator_id;
            $newActuatorId = $validatedData['actuator_id'] ?? $originalActuatorId;

            if ($originalActuatorId !== $newActuatorId) {
                $oldActuator = ActuatorCommand::find($originalActuatorId);
                if ($oldActuator) {
                    $oldActuator->auto_rule_id = null;
                    $oldActuator->save();
                }
            }
            
            $autoRule->update($validatedData);

            $newActuator = ActuatorCommand::find($newActuatorId);
            if ($newActuator) {
                $newActuator->auto_rule_id = $autoRule->id;
                $newActuator->save();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during the transaction.',
                'error_details' => $e->getMessage()
            ], 500);
        }

        // --- Start of Automatic Logging ---
        ActivityLog::create([
            'user_id'      => $request->user()->id,
            'action'       => 'updated_auto_rule',
            'subject_id'   => $autoRule->id,
            'subject_type' => get_class($autoRule),
            'description'  => "User '{$request->user()->username}' updated an auto rule: '{$autoRule->description}'.",
            'before'       => $beforeData,
            'after'        => $autoRule->fresh()->load('iotDevice', 'actuator')->toArray(),
        ]);
        // --- End of Automatic Logging ---

        return response()->json([
            'status' => 'success',
            'data' => $autoRule->fresh()->load('iotDevice', 'actuator'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AutoRule $autoRule)
    {
        $this->authorize('delete', $autoRule);
        
        $beforeData = $autoRule->load('iotDevice', 'actuator')->toArray();

        try {
            DB::beginTransaction();

            $actuator = ActuatorCommand::find($autoRule->actuator_id);
            if ($actuator) {
                $actuator->auto_rule_id = null;
                $actuator->save();
            }

            $autoRule->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during the transaction.',
                'error_details' => $e->getMessage()
            ], 500);
        }

        // --- Start of Automatic Logging ---
        ActivityLog::create([
            'user_id'      => request()->user()->id,
            'action'       => 'deleted_auto_rule',
            'subject_id'   => $beforeData['id'],
            'subject_type' => get_class($autoRule),
            'description'  => "User '" . request()->user()->username . "' deleted an auto rule: '{$beforeData['description']}'.",
            'before'       => $beforeData,
        ]);
        // --- End of Automatic Logging ---

        return response()->json([
            'status' => 'success',
            'message' => 'AutoRule and its associations have been successfully soft deleted.',
        ], 200);
    }
}
