<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = Admin::query();

            if ($request->data == 'undefined' || $request->data == "") {
                $list = $query;
            } else {

                $list = $query->where('username', 'like', '%' . $request->data . '%')
                    ->orWhere('email', 'like', '%' . $request->data . '%');
            }
            $adminList = $list->paginate(10);
            return response()->json([
                'status' => true,
                'adminList' => $adminList,
            ]);
        } catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Vui lòng nhập tên đăng nhập và mật khẩu',
                    'errors' => $validator->errors()
                ], 422);
            }
            $check = Admin::where('username', $request->username)->first();
            if ($check != '') {
                return response()->json([
                    'message' => 'username',
                    'status' => false
                ], 202);
            }
            $data = $request->only([
                'username',
                'password',
                'email',
                'display_name',
                'avatar',
                'phone',
                'status',
            ]);
            // $this->adminRepository->create($data);
            $userAdmin = new Admin();
            $userAdmin->username = $request['username'];
            $userAdmin->password = Hash::make($request['password']);
            $userAdmin->email = $request['email'];
            $userAdmin->display_name = $request['display_name'];
            //$userAdmin -> avatar = isset($request['avatar']) ? $request['avatar'] : null;

            $filePath = '';
            $disPath = public_path();


            if ($request->avatar != null) {

                $DIR = $disPath . '\uploads\admin';
                $httpPost = file_get_contents('php://input');

                $file_chunks = explode(';base64,', $request->avatar[0]);
                $fileType = explode('image/', $file_chunks[0]);
                $image_type = $fileType[0];
                $base64Img = base64_decode($file_chunks[1]);
                $data = iconv('latin5', 'utf-8', $base64Img);
                $name = uniqid();
                $file = $DIR . '\\' . $name . '.png';
                $filePath = 'admin/' . $name . '.png';
                file_put_contents($file,  $base64Img);
            }
            $userAdmin->avatar = $filePath;
            $userAdmin->phone = $request['phone'];
            $userAdmin->status = $request['status'];
            $userAdmin->save();
            return response()->json([
                'status' => true,
                'userAdmin' => $userAdmin,
            ]);
        } catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
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
        try {
            $userAdminDetail = Admin::where('id', $id)
                ->first();
            return response()->json([
                'status' => true,
                'userAdminDetail' => $userAdminDetail,
            ]);
        } catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $userAdmin = Admin::where('id', $id)->first();
            if (!isset($userAdmin)) {
                return response()->json([
                    'message' => 'name',
                    'status' => 'false'
                ], 202);
            }

            $userAdmin->email = $request['email'] ? $request['email'] : $userAdmin->email;
            $userAdmin->display_name = $request['display_name'] ? $request['display_name'] : $userAdmin->display_name;
            $userAdmin->phone = $request['phone'] ? $request['phone'] : $userAdmin->phone;
            $userAdmin->status = $request['status'] ? $request['status'] : $userAdmin->status;
            //$userAdmin->depart_id= $request['depart_id'] ? $request['depart_id']:$userAdmin->depart_id;
            //$userAdmin ->password = Hash::make($request['password'])??$userAdmin ->password;
            $filePath = '';
            $disPath = public_path();
            if ($request->avatar != null && $userAdmin->avatar !=  $request->avatar) {
                $DIR = $disPath . '\uploads\admin';
                $httpPost = file_get_contents('php://input');
                $file_chunks = explode(';base64,', $request->avatar[0]);
                $fileType = explode('image/', $file_chunks[0]);
                $image_type = $fileType[0];
                $base64Img = base64_decode($file_chunks[1]);
                $data = iconv('latin5', 'utf-8', $base64Img);
                $name = uniqid();
                $file = $DIR . '\\' . $name . '.png';
                $filePath = 'admin/' . $name . '.png';
                file_put_contents($file,  $base64Img);
            } else {
                $filePath =  $userAdmin->avatar;
            }
            $userAdmin->avatar = $filePath;
            $userAdmin->save();
            return response()->json([
                'status' => true,
                'displayName' => $userAdmin,
            ]);
        } catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Admin::where('id', $id)->delete();
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
}
