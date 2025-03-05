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
            // $now = date('d-m-Y H:i:s');
            // $stringTime = strtotime($now);
            // DB::table('adminlogs')->insert([
            //     'admin_id' => Auth::guard('admin')->user()->id,
            //     'time' =>  $stringTime,
            //     'ip' => $request->ip(),
            //     'action' => 'show all newsCategory',
            //     'cat' => 'newsCategory',
            // ]);

            if ($request->data == 'undefined' || $request->data == "") {
                $theOryCategory = TheOryCategory::all();
            } else {
                $theOryCategory = TheOryCategory::where('title', 'like', '%' . $request->data . '%')->get();
            }
            $response = [
                'status' => true,
                'list' => $theOryCategory,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $response = [
                'status' => 'false',
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
                'parentid' => 'required|integer',
                'display' => 'required|boolean',
            ]);

            $theOryCategory = new TheOryCategory();
            $theOryCategory->title = $validatedData['title'];
            $theOryCategory->description = $validatedData['description'];
            $theOryCategory->friendly_url = $validatedData['friendly_url'];
            $theOryCategory->parentid = $validatedData['parentid'];
            $theOryCategory->display = $validatedData['display'];
            $theOryCategory->save();

            return response()->json(['status' => true, 'message' => 'Category created successfully'], 201);
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

    /**
     * Show the form for editing the specified resource.
     */
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
            'parentid' => 'required|integer',
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
                'ids.*' => 'exists:blacklists,id',
            ]);

            $ids = $request->input('ids');
            if (is_array($ids)) {
                $ids = implode(",", $ids);
            }

            $idsArray = explode(",", $ids);

            foreach ($idsArray as $id) {
                TheOryCategory::whereIn('id', $idsArray)->delete();
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
