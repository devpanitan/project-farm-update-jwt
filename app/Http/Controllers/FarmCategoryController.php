<?php

namespace App\Http\Controllers;

use App\Models\FarmCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FarmCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = FarmCategory::latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cat_name' => 'required|string|max:255|unique:farm_category,cat_name',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $category = FarmCategory::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully.',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $farmCategory = FarmCategory::find($id);
        if (!$farmCategory) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $farmCategory
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $farmCategory = FarmCategory::find($id);
        if (!$farmCategory) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'cat_name' => 'sometimes|required|string|max:255|unique:farm_category,cat_name,' . $farmCategory->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $farmCategory->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully.',
            'data' => $farmCategory
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $farmCategory = FarmCategory::find($id);
        if (!$farmCategory) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }
        $farmCategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully.'
        ], 200);
    }
}
