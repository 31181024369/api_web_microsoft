<?php

namespace App\Http\Controllers\Admin;

use App\Models\TheOry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TheOryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = TheOry::query();

            if ($request->has('data') && $request->data != 'undefined' && $request->data != "") {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->data . '%')
                        ->orWhere('friendly_url', 'like', '%' . $request->data . '%');
                });
            }

            if ($request->has('cat_id') && $request->cat_id != 'undefined' && $request->cat_id != "") {
                $query->where('cat_id', $request->cat_id);
            }

            $perPage = $request->input('per_page', 10);

            $theOry = $query->paginate($perPage);

            $response = [
                'status' => true,
                'list' => $theOry->items(),
                'pagination' => [
                    'current_page' => $theOry->currentPage(),
                    'total_pages' => $theOry->lastPage(),
                    'per_page' => $theOry->perPage(),
                    'total' => $theOry->total(),
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string',
                'friendly_url' => 'required|string|max:255',
                'meta_keywords' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:255',
                'picture' => 'nullable',
                'display' => 'required|boolean',
                'cat_id' => 'required|exists:theory_category,cat_id',
            ]);

            $theOry = new TheOry();
            $theOry->title = $validatedData['title'];
            $theOry->description = $validatedData['description'] ?? null;
            $theOry->short_description = $validatedData['short_description'] ?? null;
            $theOry->friendly_url = $validatedData['friendly_url'];
            $theOry->meta_keywords = $validatedData['meta_keywords'] ?? null;
            $theOry->meta_description = $validatedData['meta_description'] ?? null;

            $filePath = '';
            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = 'uploads/admin/' . $fileName;
                $file->move(public_path('uploads/admin'), $fileName);
            }
            $theOry->picture = $filePath;
            $theOry->display = $validatedData['display'];
            $theOry->cat_id = $validatedData['cat_id'];
            $theOry->save();

            return response()->json([
                'status' => true,
                'message' => 'success'
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

    public function show(string $id) {}

    public function edit(string $id)
    {
        try {
            $theOry = TheOry::findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $theOry
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
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

    public function delete(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|exists:theory,theory_id',
            ]);

            $theOry = TheOry::findOrFail($validatedData['id']);
            $theOry->delete();

            return response()->json([
                'status' => true,
                'message' => 'success'
            ], 200);
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
}
