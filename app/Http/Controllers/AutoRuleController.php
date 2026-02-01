<?php

namespace App\Http\Controllers;

use App\Models\AutoRule;
use App\Models\ActuatorCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AutoRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => AutoRule::with('iotDevice', 'actuator')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        try {
            DB::beginTransaction();

            // 1. Create the AutoRule
            $autoRule = AutoRule::create($validatedData);

            // 2. Update the ActuatorCommand with the new auto_rule_id
            $actuator = ActuatorCommand::find($validatedData['actuator_id']);
            $actuator->auto_rule_id = $autoRule->id;
            $actuator->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $autoRule->load('iotDevice', 'actuator'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during the transaction.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AutoRule $autoRule)
    {
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

        try {
            DB::beginTransaction();

            $originalActuatorId = $autoRule->actuator_id;
            $newActuatorId = $validatedData['actuator_id'] ?? $originalActuatorId;

            // If actuator is being changed, release the old one
            if ($originalActuatorId !== $newActuatorId) {
                $oldActuator = ActuatorCommand::find($originalActuatorId);
                if ($oldActuator) {
                    $oldActuator->auto_rule_id = null;
                    $oldActuator->save();
                }
            }
            
            // Update the AutoRule itself
            $autoRule->update($validatedData);

            // Link the new actuator
            $newActuator = ActuatorCommand::find($newActuatorId);
            if ($newActuator) {
                $newActuator->auto_rule_id = $autoRule->id;
                $newActuator->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $autoRule->fresh()->load('iotDevice', 'actuator'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during the transaction.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AutoRule $autoRule)
    {
        try {
            DB::beginTransaction();

            // 1. Find the associated actuator and release it
            $actuator = ActuatorCommand::find($autoRule->actuator_id);
            if ($actuator) {
                $actuator->auto_rule_id = null;
                $actuator->save();
            }

            // 2. Delete the auto rule
            $autoRule->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'AutoRule and its associations have been successfully soft deleted.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during the transaction.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }
}
