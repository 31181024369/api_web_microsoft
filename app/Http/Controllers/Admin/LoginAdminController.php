<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class LoginAdminController extends Controller
{
    //
    public function login(Request $request){
        try{
            $val = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);
            if ($val->fails()) {
                return response()->json($val->errors(), 202);
            }
            $admin = Admin::where('username',$request->username)->first();
            if(isset($admin)!=1)
            {
                return response()->json([
                    'status' => false,
                    'mess' => 'username'
                ]);
            }
            $check =  $admin->makeVisible('password');


            if(Hash::check($request->password,$check->password)){

                    $success= $admin->createToken('Admin')->accessToken;
                    return response()->json([
                            'status' => true,
                            'token' => $success,
                            'username'=>$admin->display_name
                        ]);
            }else {

                return response()->json([
                        'status' => false,
                        'mess' => 'pass'
                ]);
            }



        }catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }

    }
    public function information(Request $request){
        try{
            $id = Auth::guard('admin')->user()->id;
            $userAdmin = Admin::where('id',$id)->first();
            return response()->json([
                'status'=>true,
                'data'=> $userAdmin,
            ]);

        }catch (\Exception $error) {
            return response()->json([
                'status_code' => 500,
                'message' => 'error.',
                'error' => $error->getMessage()
            ], 500);
        }
    }
    public function uploadInformation(Request $request){
        try{
            $id = Auth::guard('admin')->user()->id;
            $userAdmin = Admin::where('id',$id)->first();

            $userAdmin->email = $request['email'] ? $request['email']:$userAdmin->email;
            $userAdmin->display_name = $request['display_name'] ? $request['display_name']: $userAdmin -> display_name ;
            $userAdmin->phone = $request['phone'] ? $request['phone']:$userAdmin ->phone;
            $userAdmin->status = $request['status'] ? $request['status']:$userAdmin->status;

            $filePath = '';
            $disPath = public_path();
            if ( $request->avatar!= null && $userAdmin->avatar !=  $request->avatar )
            {
                $DIR = $disPath.'\uploads\admin';
                $httpPost = file_get_contents( 'php://input' );
                $file_chunks = explode( ';base64,', $request->avatar[ 0 ] );
                $fileType = explode( 'image/', $file_chunks[ 0 ] );
                $image_type = $fileType[ 0 ];
                $base64Img = base64_decode( $file_chunks[ 1 ] );
                $data = iconv( 'latin5', 'utf-8', $base64Img );
                $name = uniqid();
                $file = $DIR .'\\'. $name . '.png';
                $filePath = 'admin/'.$name . '.png';
                file_put_contents( $file,  $base64Img );
            }
            else{
                $filePath =  $userAdmin->avatar;
            }
            $userAdmin->avatar=$filePath;
            $userAdmin->save();
            return response()->json([
                'status' => true,
                'data' => $userAdmin,
            ]);


        }catch (\Exception $error) {
            return response()->json([
                'status_code' => 500,
                'message' => 'error.',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
