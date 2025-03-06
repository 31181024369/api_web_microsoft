<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\TheOryCategory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class TheOryCategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = TheOryCategory::query();

            if ($request->data != 'undefined' && $request->data != "") {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->data . '%')
                        ->orWhere('friendly_url', 'like', '%' . $request->data . '%');
                });
            }

            $perPage = $request->input('per_page', 10);

            $theOryCategory = $query->orderBy('cat_id', 'desc')->paginate($perPage);

            $theOryCategory = $query->paginate($perPage);

            $response = [
                'status' => true,
                'list' => $theOryCategory->items(),
                'pagination' => [
                    'current_page' => $theOryCategory->currentPage(),
                    'total_pages' => $theOryCategory->lastPage(),
                    'per_page' => $theOryCategory->perPage(),
                    'total' => $theOryCategory->total(),
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

    public function create()
    {
        return 111;
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'friendly_url' => 'required|string|max:255',
                'display' => 'required|boolean',
            ]);

            $theOryCategory = new TheOryCategory();
            $theOryCategory->title = $validatedData['title'];
            $theOryCategory->description = $validatedData['description'];
            $theOryCategory->friendly_url = $validatedData['friendly_url'];
            $theOryCategory->display = $validatedData['display'];
            $theOryCategory->save();

            return response()->json(['status' => true, 'message' => 'success'], 201);
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

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        try {
            $theOryCategory = TheOryCategory::findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $theOryCategory
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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'friendly_url' => 'required|string|max:255',
            'parentid' => 'nullable|integer',
            'display' => 'required|boolean',
        ]);

        try {
            $theOryCategory = TheOryCategory::findOrFail($id);
            $theOryCategory->title = $request->title;
            $theOryCategory->description = $request->description;
            $theOryCategory->friendly_url = $request->friendly_url;
            $theOryCategory->parentid = $request->parentid;
            $theOryCategory->display = $request->display;
            $theOryCategory->save();

            return response()->json([
                'status' => true,
                'message' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(
                ['status' => false, 'error' => $e->getMessage()],
                500
            );
        }
    }

    public function destroy(string $id)
    {
        try {
            $theOryCategory = TheOryCategory::findOrFail($id);
            $theOryCategory->delete();

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
                'ids.*' => 'exists:theory_category,cat_id',
            ]);

            $ids = $request->input('ids');
            if (is_array($ids)) {
                $ids = implode(",", $ids);
            }

            $idsArray = explode(",", $ids);

            foreach ($idsArray as $id) {
                TheOryCategory::whereIn('cat_id', $idsArray)->delete();
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
