<?php

namespace App\Http\Controllers\Admin;

use App\Models\GiftCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GiftCategoryController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = GiftCategory::query();

            if ($request->data != 'undefined' && $request->data != "") {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->data . '%');
                });
            }

            $perPage = $request->input('per_page', 10);

            $GiftCategory = $query->paginate($perPage);

            $response = [
                'status' => true,
                'list' => $GiftCategory->items(),
                'pagination' => [
                    'current_page' => $GiftCategory->currentPage(),
                    'total_pages' => $GiftCategory->lastPage(),
                    'per_page' => $GiftCategory->perPage(),
                    'total' => $GiftCategory->total(),
                ],
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $response = [
                'status' => false,
                'error' => $errorMessage
            ];
            return response()->json($response, 500);
        }
    }


    public function create() {}

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'reward_point' => 'required|integer',
                'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'display' => 'required|boolean',
            ]);

            $giftCategory = new GiftCategory();
            $giftCategory->title = $validatedData['title'];
            $giftCategory->description = $validatedData['description'] ?? null;
            $giftCategory->reward_point = $validatedData['reward_point'];
            $giftCategory->display = $validatedData['display'];

            $filePath = '';
            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'uploads/gift/' . $fileName;
                $file->move(public_path('uploads/gift'), $fileName);
            }
            $giftCategory->picture = $filePath;
            $giftCategory->save();

            return response()->json([
                'status' => true,
                'message' => 'Gift category created successfully',
                'data' => $giftCategory
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
