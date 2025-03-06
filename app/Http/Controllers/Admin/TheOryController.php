<?php

namespace App\Http\Controllers\Admin;

use App\Models\TheOry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

            $theOry = $query->with('category:cat_id,title')->paginate($perPage);

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
                'picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
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
                $image = $request->file('picture');
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/theory'), $imageName);
                $filePath = 'uploads/theory/' . $imageName;
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

    public function update(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string',
                'friendly_url' => 'required|string|max:255',
                'meta_keywords' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:255',
                'picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'display' => 'required|boolean',
                'cat_id' => 'required|exists:theory_category,cat_id',
            ]);

            $theOry = TheOry::findOrFail($id);
            $theOry->title = $validatedData['title'];
            $theOry->description = $validatedData['description'] ?? null;
            $theOry->short_description = $validatedData['short_description'] ?? null;
            $theOry->friendly_url = $validatedData['friendly_url'];
            $theOry->meta_keywords = $validatedData['meta_keywords'] ?? null;
            $theOry->meta_description = $validatedData['meta_description'] ?? null;

            $filePath = $theOry->picture;
            if ($request->hasFile('picture')) {
                $image = $request->file('picture');
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/theory'), $imageName);
                $filePath = 'uploads/theory/' . $imageName;
            }

            $theOry->picture = $filePath;
            $theOry->display = $validatedData['display'];
            $theOry->cat_id = $validatedData['cat_id'];
            $theOry->save();

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

    public function destroy(string $id)
    {
        try {
            $theory = TheOry::findOrFail($id);
            $theory->delete();

            return response()->json([
                'status' => true,
                'message' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:theory,theory_id',
            ]);

            $ids = $request->input('ids');
            if (is_array($ids)) {
                $ids = implode(",", $ids);
            }

            $idsArray = explode(",", $ids);

            foreach ($idsArray as $id) {
                TheOry::whereIn('theory_id', $idsArray)->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xóa dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }
}
