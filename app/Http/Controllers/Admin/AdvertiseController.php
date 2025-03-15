<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Adpos;
use App\Models\Advertise;
use App\Models\AdminLogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class AdvertiseController extends Controller
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
            $pos = $request['id_pos'];
            $query = Advertise::with('Adpos')->orderBy('id', 'desc');
            if (empty($request->input('data')) || $request->input('data') == 'undefined' || $request->input('data') == '') {
                $list = $query;
            } else {
                $list = $query->where("title", 'like', '%' . $request->input('data') . '%');
            }
            if (isset($pos)) {
                $list = $query->where("id_pos", $pos);
            }
            $listAdvertise = $list->paginate(10);
            $response = [
                'status' => true,
                'list' => $listAdvertise
            ];

            $this->createLog(
                auth()->guard('admin')->id(),
                'VIEW',
                'ADVERTISE',
                'Xem danh sách quảng cáo'
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

    public function store(Request $request)
    {
        try {

            $disPath = public_path();
            $advertise = new Advertise();
            $filePath = '';
            if ($request->picture != null) {

                $DIR = $disPath . '\uploads\advertise';
                $httpPost = file_get_contents('php://input');
                $file_chunks = explode(';base64,', $request->picture[0]);
                $fileType = explode('image/', $file_chunks[0]);
                $image_type = $fileType[0];

                //return response()->json( $file_chunks );
                $base64Img = base64_decode($file_chunks[1]);
                $data = iconv('latin5', 'utf-8', $base64Img);
                $name = uniqid();
                $file = $DIR . '\\' . $name . '.png';
                $filePath = 'advertise/' . $name . '.png';

                file_put_contents($file,  $base64Img);
            }
            $advertise->title = $request->title;
            $advertise->picture = $filePath;
            $advertise->id_pos = $request->id_pos;
            $advertise->width = $request->width;
            $advertise->height = $request->height;
            $advertise->link = $request->link ? $request->link : '#';
            $advertise->description = $request->description ? $request->description : 0;
            $advertise->display = $request->display;
            $advertise->save();

            $this->createLog(
                auth()->guard('admin')->id(),
                'CREATE',
                'ADVERTISE',
                'Tạo quảng cáo mới: ' . $advertise->title
            );

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

    public function edit(string $id)
    {
        try {
            $list = Advertise::find($id);
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
            $disPath = public_path();

            $advertise = Advertise::Find($id);
            $filePath = '';
            if ($request->picture != null && $advertise->picture != $request->picture) {
                $filePath = '';
                $DIR = $disPath . '\uploads\advertise';
                $httpPost = file_get_contents('php://input');
                $file_chunks = explode(';base64,', $request->picture[0]);
                $fileType = explode('image/', $file_chunks[0]);
                $image_type = $fileType[0];

                //return response()->json( $file_chunks );
                $base64Img = base64_decode($file_chunks[1]);
                $data = iconv('latin5', 'utf-8', $base64Img);
                $name = uniqid();
                $file = $DIR . '\\' . $name . '.png';
                $filePath = 'advertise/' . $name . '.png';

                file_put_contents($file,  $base64Img);
            } else {
                $filePath = $advertise->picture;
            }

            $advertise->title = $request->title;
            $advertise->picture = $filePath;
            $advertise->id_pos = $request->id_pos;
            $advertise->width = $request->width;
            $advertise->height = $request->height;
            $advertise->link = $request->link ? $request->link : '#';
            $advertise->description = $request->description ? $request->description : 0;
            $advertise->display = $request->display;
            $advertise->save();

            $this->createLog(
                auth()->guard('admin')->id(),
                'UPDATE',
                'ADVERTISE',
                'Cập nhập quảng cáo: ' . $advertise->title
            );
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


    public function destroy(string $id)
    {
        try {
            $advertise = Advertise::find($id);

            if ($advertise) {
                $title = $advertise->title;

                if ($advertise->picture) {
                    $imagePath = public_path('uploads/' . $advertise->picture);
                    if (File::exists($imagePath)) {
                        File::delete($imagePath);
                    }
                }

                $advertise->delete();

                $this->createLog(
                    auth()->guard('admin')->id(),
                    'DELETE',
                    'ADVERTISE',
                    'Xóa quảng cáo: ' . $title
                );
            }

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

    public function deleteAll(Request $request)
    {
        $arr = $request->data;
        try {
            if ($arr) {
                $advertiseNames = Advertise::whereIn('id', $arr)
                    ->pluck('title')
                    ->toArray();
                foreach ($arr as $item) {
                    $advertise = Advertise::find($item);
                    if ($advertise) {
                        if ($advertise->picture) {
                            $imagePath = public_path('uploads/' . $advertise->picture);
                            if (File::exists($imagePath)) {
                                File::delete($imagePath);
                            }
                        }
                        $advertise->delete();
                    }
                }
                $this->createLog(
                    auth()->guard('admin')->id(),
                    'DELETE',
                    'ADVERTISE',
                    'Xóa nhiều quảng cáo: ' . implode(', ', $advertiseNames)
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
                'status' => false,
                'error' => $errorMessage
            ];
            return response()->json($response, 500);
        }
    }
}
