<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
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
                ->orWhere('email','like', '%' . $request->data . '%');
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
            $member=Member::where('id',$id)->first();
            $member -> email = $request->email;
            $member -> full_name = $request->fullname;
            $member -> phone = $request->phone;
            $member ->nameCompany=$request->nameCompany;
            $member -> tax = $request->tax;
            $member ->m_status = $request->m_status;
            $member->save();
            if($request->m_status==1){

            }

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
