<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Adpos;
use App\Models\Advertise;
use App\Models\AdminLogs;
use Carbon\Carbon;

class AdposController extends Controller
{
    private function createLog($adminId, $action, $cat, $description)
    {
        AdminLogs::create([
            'admin_id' => $adminId,
            'time' => Carbon::now(),
            'action' => $action,
            'cat' => $cat,
            'description' => $description
        ]);
    }

    public function index(Request $request)
    {
        try {
            $query = Adpos::query();
            if ($request->data == 'undefined' || $request->data == "") {
                $Adpos = $query;
            } else {
                $Adpos = $query->where("title", 'like', '%' . $request->data . '%');
            }
            $list = $Adpos->orderBy('id_pos', 'desc')->paginate(10);
            $response = [
                'status' => true,
                'list' => $list
            ];

            $this->createLog(
                auth()->guard('admin')->id(),
                'VIEW',
                'ADPOS',
                'Xem danh mục quảng cáo'
            );

            return response()->json($response, 200);
        } catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function create() {}

    public function store(Request $request)
    {
        try {

            $adpos = new Adpos();
            $adpos->fill([
                'name' => $request->input('name'),
                'title' => $request->input('title'),
                'width' => $request->input('width'),
                'height' => $request->input('height'),
                'description' => $request->input('description'),
                'display' => $request->input('display'),
            ])->save();

            $this->createLog(
                auth()->guard('admin')->id(),
                'CREATE',
                'ADPOS',
                'Tạo danh mục quảng cáo mới: ' . $adpos->name
            );

            $response = [
                'status' => true,
                'adpos' => $adpos
            ];
            return response()->json($response, 200);
        } catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function show(string $id) {}

    public function edit(string $id)
    {
        try {
            $list = Adpos::find($id);
            return response()->json([
                'status' => true,
                'list' => $list
            ]);
        } catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $listAdpos = Adpos::Find($id);
            $listAdpos->fill([
                'name' => $request->input('name'),
                'title' => $request->input('title'),
                'width' => $request->input('width'),
                'height' => $request->input('height'),
                'description' => $request->input('description'),
                'display'  => $request->input('display'),
            ])->save();

            $this->createLog(
                auth()->guard('admin')->id(),
                'UPDATE',
                'ADPOS',
                'Cập nhập danh mục quảng cáo: ' . $listAdpos->name
            );

            return response()->json([
                'status' => true
            ]);
        } catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $Adpos = Adpos::where('id_pos', $id)->first();

            if ($Adpos) {
                $adposName = $Adpos->name;
                Advertise::where('id_pos', $id)->delete();
                $Adpos->delete();

                $this->createLog(
                    auth()->guard('admin')->id(),
                    'DELETE',
                    'ADPOS',
                    'Xóa danh mục quảng cáo: ' . $adposName
                );
            }

            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }
    public function deleteAll(Request $request)
    {
        $arr = $request->data;

        try {
            if ($arr) {
                $deletedNames = Adpos::whereIn('id_pos', $arr)->pluck('name')->toArray();
                foreach ($arr as $item) {
                    Adpos::Find($item)->delete();
                }

                $this->createLog(
                    auth()->guard('admin')->id(),
                    'DELETE',
                    'ADPOS',
                    'Xóa nhiều danh mục quảng cáo: ' . implode(', ', $deletedNames)
                );
            } else {
                return response()->json([
                    'status' => false,
                ], 422);
            }
            return response()->json([
                'status' => true,
            ], 200);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $response = [
                'status' => 'false',
                'error' => $errorMessage
            ];
            return response()->json($response, 500);
        }
    }
}
