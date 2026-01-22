<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserCatController extends Controller
{
    // In-memory data for testing purposes
    private static $cats = [
        ['id' => 1, 'name' => 'Whiskers', 'created_at' => '2023-01-15T10:00:00Z'],
        ['id' => 2, 'name' => 'Garfield', 'created_at' => '2023-02-20T11:30:00Z'],
        ['id' => 3, 'name' => 'Tom', 'created_at' => '2023-03-25T12:45:00Z'],
    ];

    /**
     * Display a listing of the cats.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Cats fetched successfully.',
            'data' => self::$cats
        ]);
    }

    /**
     * Store a newly created cat in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $newId = count(self::$cats) > 0 ? max(array_column(self::$cats, 'id')) + 1 : 1;

        $newCat = [
            'id' => $newId,
            'name' => $request->name,
            'created_at' => now()->toIso8601String(),
        ];

        self::$cats[] = $newCat;

        return response()->json([
            'status' => 'success',
            'message' => 'Cat created successfully.',
            'data' => $newCat
        ], 201);
    }

    /**
     * Display the specified cat.
     */
    public function show(string $id)
    {
        $cat = Arr::first(self::$cats, fn ($cat) => $cat['id'] == $id);

        if (!$cat) {
            return response()->json(['status' => 'error', 'message' => 'Cat not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $cat
        ]);
    }

    /**
     * Update the specified cat in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        $key = array_search($id, array_column(self::$cats, 'id'));

        if ($key === false) {
            return response()->json(['status' => 'error', 'message' => 'Cat not found'], 404);
        }

        self::$cats[$key]['name'] = $request->name ?? self::$cats[$key]['name'];

        return response()->json([
            'status' => 'success',
            'message' => 'Cat updated successfully.',
            'data' => self::$cats[$key]
        ]);
    }

    /**
     * Remove the specified cat from storage.
     */
    public function destroy(string $id)
    {
        $key = array_search($id, array_column(self::$cats, 'id'));

        if ($key === false) {
            return response()->json(['status' => 'error', 'message' => 'Cat not found'], 404);
        }

        array_splice(self::$cats, $key, 1);

        return response()->json([
            'status' => 'success',
            'message' => 'Cat deleted successfully.'
        ], 200);
    }
}
