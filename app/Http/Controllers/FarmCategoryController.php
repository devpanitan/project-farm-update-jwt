<?php

namespace App\Http\Controllers;

use App\Models\FarmCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class FarmCategoryController extends Controller
{
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // 1. Protect all methods with auth:api middleware for authentication
        $this->middleware('auth:api');

        // 2. Authorize write actions (store, update, destroy) for Super Admins only
        // We use the 'can' middleware with the Gate we defined earlier.
        // This will automatically check if Gate::allows('isSuperAdmin') returns true.
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('isSuperAdmin')) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        })->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     * Accessible to any authenticated user.
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
     * Super Admins only.
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
     * Accessible to any authenticated user.
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
     * Super Admins only.
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
     * Super Admins only.
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
