<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $farms = Farm::with('farmCategory')->latest()->get();

        // Check if the request wants a JSON response (API call)
        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'data' => $farms]);
        }

        // Otherwise, return the web view with the farms data
        return view('welcome', ['farms' => $farms]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'farm_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'farm_category_id' => 'required|exists:farm_category,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'size' => 'nullable|numeric',
            'farm_prefix' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $farmData = [
            'name' => $validatedData['farm_name'],
            'farm_cat_id' => $validatedData['farm_category_id'],
            'description' => $validatedData['description'] ?? null,
            'lat' => $validatedData['latitude'] ?? null,
            'lng' => $validatedData['longitude'] ?? null,
            'size' => $validatedData['size'] ?? null,
            'farm_prefix' => $validatedData['farm_prefix'] ?? null,
        ];

        $farm = Farm::create($farmData);
        
        return response()->json(['status' => 'success', 'message' => 'Farm created successfully.', 'data' => $farm->load('farmCategory')], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $farm = Farm::with('farmCategory')->find($id);
        if (!$farm) {
            return response()->json(['status' => 'error', 'message' => 'Farm not found.'], 404);
        }
        
        // This is an API-only method, so it always returns JSON.
        return response()->json(['status' => 'success', 'data' => $farm]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $farm = Farm::find($id);
        if (!$farm) {
            return response()->json(['status' => 'error', 'message' => 'Farm not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'farm_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'farm_category_id' => 'sometimes|required|exists:farm_category,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'size' => 'nullable|numeric',
            'farm_prefix' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        
        $farmData = [];
        if (isset($validatedData['farm_name'])) $farmData['name'] = $validatedData['farm_name'];
        if (isset($validatedData['farm_category_id'])) $farmData['farm_cat_id'] = $validatedData['farm_category_id'];
        if (array_key_exists('description', $validatedData)) $farmData['description'] = $validatedData['description'];
        if (isset($validatedData['latitude'])) $farmData['lat'] = $validatedData['latitude'];
        if (isset($validatedData['longitude'])) $farmData['lng'] = $validatedData['longitude'];
        if (isset($validatedData['size'])) $farmData['size'] = $validatedData['size'];
        if (isset($validatedData['farm_prefix'])) $farmData['farm_prefix'] = $validatedData['farm_prefix'];

        if (!empty($farmData)) {
            $farm->update($farmData);
        }

        return response()->json(['status' => 'success', 'message' => 'Farm updated successfully.', 'data' => $farm->load('farmCategory')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $farm = Farm::find($id);
        if (!$farm) {
            return response()->json(['status' => 'error', 'message' => 'Farm not found.'], 404);
        }
        $farm->delete();
        return response()->json(['status' => 'success', 'message' => 'Farm deleted successfully.']);
    }
}
