<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Adpos;
use App\Models\Advertise;
class AdposController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query=Adpos::query();
            if($request->data == 'undefined' || $request->data =="")
            {
                $Adpos = $query;
            }
            else{
                $Adpos = $query->where("title", 'like', '%' . $request->data . '%');
            }
            $list=$list->orderBy('id_pos','desc')->paginate(10);
            $response = [
                'status' => true,
                'list' => $list
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
            $adpos = new Adpos();
            $adpos->fill([
                'name' => $request->input('name'),
                'title' => $request->input('title'),
                'width' => $request->input('width'),
                'height' => $request->input('height'),
                'description' => $request->input('description'),
                'display' => $request->input('display'),
            ])->save();
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
            $list = Adpos::find($id);
            return response()->json([
                'status'=> true,
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

    /**
     * Update the specified resource in storage.
     */
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
            return response()->json([
                'status'=>true
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
            $Adpos=Adpos::where('is_pos',$id)->first();
            if($Adpos){
                Advertise::where('is_pos',$id)->delete();
            }
            $list = Adpos::Find($id)->delete();

            return response()->json([
                'status'=> true,
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
