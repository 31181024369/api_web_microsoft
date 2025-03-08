<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Adpos;
use App\Models\Advertise;
class AdvertiseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $pos=$request['id_pos'];
            $module_show=$request['module_show'];
            $query=Advertise::orderBy('id','desc');
            if(empty($request->input('data'))||$request->input('data')=='undefined' ||$request->input('data')=='')
            {
                $list = $query;
            }
            else{
                $list = $query->where("title", 'like', '%' . $request->input('data') . '%');
            }
            if(isset($pos)){
                $list = $query->where("id_pos",$pos);
            }
            $listAdvertise=$list->paginate(10);
            $response = [
                'status' => true,
                'list' => $listAdvertise
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
        try{
            $disPath = public_path();
            $advertise = new Advertise();
            $filePath = '';
            if ( $request->picture != null ) {

                $DIR = $disPath.'\uploads\advertise';
                $httpPost = file_get_contents( 'php://input' );
                $file_chunks = explode( ';base64,', $request->picture[ 0 ] );
                $fileType = explode( 'image/', $file_chunks[ 0 ] );
                $image_type = $fileType[ 0 ];

                //return response()->json( $file_chunks );
                $base64Img = base64_decode( $file_chunks[ 1 ] );
                $data = iconv( 'latin5', 'utf-8', $base64Img );
                $name = uniqid();
                $file = $DIR .'\\'. $name . '.png';
                $filePath = 'advertise/'.$name . '.png';

                file_put_contents( $file,  $base64Img );
            }
            $advertise-> title = $request->title;
            $advertise-> picture = $filePath;
            $advertise-> pos = $request->pos;
            $advertise-> width = $request->width;
            $advertise-> height = $request->height;
            $advertise-> link = $request->link?$request->link:'#';
            $advertise-> target = $request->target?$request->target:'_self';
            // $advertise-> module_show = $module;
            $advertise-> description = $request->description?$request->description:0;
            $advertise-> menu_order = $request->menu;
            $advertise-> display = $request->display;
            $advertise-> lang = 'vi';
            $advertise->save();
            return response()->json( [
                'status'=>true,
            ] );


        }catch (\Exception $error) {

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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
