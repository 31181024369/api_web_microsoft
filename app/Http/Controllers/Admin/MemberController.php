<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Mail\Notification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $query=Member::query();
            if($request->data == 'undefined' || $request->data =="")
            {
                $member=$query;
            }
            else{
                $member=$query->where('username','like', '%' . $request->data . '%')
                ->orWhere('email','like', '%' . $request->data . '%')
                ->orWhere('tax','like', '%' . $request->data . '%')
                ->orWhere('full_name','like', '%' . $request->data . '%')
                ;
            }
            $member=$member->orderBy('id','desc')->paginate(10);
            return response()->json([
                'status'=>true,
                'data'=>$member
            ]);

        }catch (\Exception $error) {

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try{
            $member=Member::where('id',$id)->first();
            return response()->json([
                'status'=>true,
                'data'=>$member
            ]);
        }catch (\Exception $error) {

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
        try{

            $date=Carbon::now('Asia/Ho_Chi_Minh')->isoFormat('DD/MM/YYYY');
            $member=Member::where('id',$id)->first();
            $member -> email = $request->email??$member->email;
            $member -> full_name = $request->fullname??$member->full_name;
            $member -> phone = $request->phone??$member->phone;
            $member ->nameCompany=$request->nameCompany??$member->nameCompany;
            $member -> tax = $request->tax??$member->tax;
            $member ->m_status = $request->m_status??$member->m_status;
            $member ->points = $request->points??$member->points;
            $member ->used_points = $request->used_points??$member->used_points;
            $member ->number_passes = $request->number_passes??$member->number_passes;
            $member->save();
            if($request->m_status==1){
                $to = $member -> email;

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

            }
            return response()->json([
                'status'=> true,
            ]);

        }catch (\Exception $error) {

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
        try{
            $member=Member::where('id',$id)->first();
            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Member not found'
                ], 404);
            }
            $member->delete();


        }catch (\Exception $error) {

            return response()->json([
                'status_code' => 500,
                'message' => 'error',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
