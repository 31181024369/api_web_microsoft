<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Mail\Notification;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function register(Request $request)
    {
        try {


            $date = Carbon::now('Asia/Ho_Chi_Minh');
            $timestamp = strtotime($date);
            // check null fill
            $username = isset($request->accountName) ? $request->accountName : '';
            $password = isset($request->password) ? $request->password : '';
            $full_name = isset($request->fullName) ? $request->fullName : '';
            $email = isset($request->email) ? $request->email : '';
            $mem_code = isset($request->mem_code) ? $request->mem_code : '';


            $phone = isset($request->numberPhone) ? $request->numberPhone : '';


            $address =  isset($request->address) ? $request->address : '';
            $company =  isset($request->company) ? $request->company : '';
            $district =  isset($request->district) ? $request->district : '';
            $ward =  isset($request->ward) ? $request->ward : '';
            $province =  isset($request->province) ? $request->province : '';
            $nameCompany =  isset($request->nameCompany) ? $request->nameCompany : '';
            $tax =  isset($request->tax) ? $request->tax : '';

            $isExistEmail = Member::where("email", $email)
                ->first();
            $isExistUsername = Member::where("username", $username)
                ->first();

            if ($isExistUsername) {
                return response()->json(['message' => 'existUserName', 'status' => false]);
            }
            if ($isExistEmail) {
                return response()->json(['message' => 'existEmail', 'status' => false]);
            } else {

                $member = Member::create([
                    'username' => $username,
                    'mem_code' =>  $mem_code,
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
                    'date_join' => $timestamp,
                    'm_status' => 0,
                    'status' => 0,
                    'nameCompany' => $nameCompany,
                    'tax' => $tax
                ]);
                $data = [
                    'subject' => 'Tài Khoản Của Bạn Đã Được Kích Hoạt',
                    'body' => '
                    Kính gửi '. $member -> full_name .'<br>
                    Chúc mừng! Tài khoản Microsoft của bạn đã được kích hoạt thành công.
                    Bạn có thể bắt đầu sử dụng các dịch vụ của chúng tôi ngay bây giờ.<br>
                    Dưới đây là thông tin tài khoản của Quý Khách:<br>
                    Tên đăng nhập: '.$member -> username.'<br>
                    Tên doanh nghiệp:'. $member ->nameCompany.'<br>
                    Mã số thuế:'.$member -> tax.'<br>
                    Thời gian đăng kí: '.$date.'<br>
                    Nếu bạn không yêu cầu kích hoạt tài khoản này, vui lòng liên hệ với bộ phận hỗ trợ của chúng tôi để được
                    trợ giúp.Trân trọng, <br>
                    Đội ngũ Microsoft<br>'
                ];
                Mail::to($to)
                ->send(new Notification($data));


                return response()->json([
                    'message' => 'Đăng ký thành công',
                    'data' => [
                        // 'Id' => $member->MaKH,
                        'TenDD' => $member->username,
                        'Email' => $member->email,
                        'Phone' => $member->phone,
                    ],
                    'status' => true,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function login(Request $request)
    {
        try {

            $member = Member::where('username', $request->username)
                ->first();

            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'userNotExist'
                ]);
            }
            $abbreviation = "";
            $string = ucwords($member->password);
            $words = explode(" ", "$string");
            foreach ($words as $word) {
                $abbreviation .= $word[0];
            }



            if (isset($member) && $abbreviation != "$" && Hash::check($request->password, $member->password) == false) {
                Member::where('id', $member->id)->first()->update(['password' => Hash::make($request->password)]);
            }

            if ($member && $abbreviation == "$" && Hash::check($request->password, $member->password)) {
                if($member->m_status == 0)
                {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unapproved account'
                    ]);
                }

                $success = $member->createToken('Member')->accessToken;
                return response()->json([
                    'status' => true,
                    'token' => $success,
                    'member' => $member
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'wrongPassword'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function inforMember(Request $request)
    {
        try {
            $member = Auth::guard('member')->user();
            if ($member) {
                return response()->json([
                    'status' => true,
                    'data' => $member
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'data' => null
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
