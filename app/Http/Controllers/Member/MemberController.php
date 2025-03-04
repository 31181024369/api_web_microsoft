<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class MemberController extends Controller
{
    public function register(Request $request){
        try{


            $date = Carbon::now('Asia/Ho_Chi_Minh');
            $timestamp = strtotime($date);
            // check null fill

            $username = isset($request->accountName) ? $request->accountName : '';
            $password = isset($request->password) ? $request->password : '';
            $full_name = isset($request->fullName) ? $request->fullName : '';
            $gender = isset($request->gender) ? $request->gender : '';
            $email = isset($request->email) ? $request->email : '';
            $mem_code = isset($request->mem_code) ? $request->mem_code : '';


            $phone = isset($request->numberPhone) ? $request->numberPhone : '';


            $address =  isset($request->address) ? $request->address : '';
            $company =  isset($request->company) ? $request->company : '';
            $district =  isset($request->district) ? $request->district : '';
            $ward =  isset($request->ward) ? $request->ward : '';
            $province =  isset($request->province) ? $request->province : '';

            $isExistEmail = Member::where("email", $email)
                ->first();
            $isExistUsername = Member::where("username", $username)
            ->first();

            if ($isExistUsername ) {
                return response()->json(['message'=>'existUserName', 'status' => false]);
            }
            if ($isExistEmail ) {
                return response()->json(['message'=>'existEmail', 'status' => false]);
            }else{

                $member = Member::create([
                    'username' => $username,
                    'mem_code' =>  $mem_code,
                    'gender' => $gender,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'address' => $address,
                    // 'company' => $company,
                    'full_name' => $full_name,
                    'avatar' => '',
                    'phone' => $phone,
                    // 'provider' =>'',
                    'provider_id' => '',
                    'ward' => $ward,
                    'district' => $district,
                    'city_province' => $province,
                    'date_join'=>$timestamp,
                    'm_status' => 0,
                    'status' => 0

                ]);
                return response()->json([
                    'message'=> 'Đăng ký thành công',
                    'data' => [
                        // 'Id' => $member->MaKH,
                        'TenDD' => $member->username,
                        'Email' => $member->email,
                        'Phone' => $member->phone,
                    ],
                    'status'=> true,
                ]);
            }
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }

    }
    public function login(Request $request){
        try{

            $member=Member::where('username',$request->username)
            ->first();

            if(!$member)
            {
                return response()->json([
                    'status' =>false,
                    'message' => 'userNotExist'
                    ]);
            }
            $abbreviation = "";
            $string = ucwords($member->password);
            $words = explode(" ", "$string");
            foreach($words as $word){
                $abbreviation .= $word[0];
            }


            if(isset($member) && $abbreviation != "$" && Hash::check($request->password,$member->password)==false)
            {
                Member::where('id', $member->id)->first()->update(['password' => Hash::make($request->password)]);
            }

            if( $member && $abbreviation == "$" && Hash::check($request->password,$member->password)){

                return response()->json([
                    'status' => true,
                    'member' => $member
                    ]);
            }else {
                return response()->json([
                    'status'=>false,
                    'message' => 'wrongPassword'
                ]);
            }
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }

    }
}
