<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $query = Product::query();

            if (!empty($request->input('data')) && $request->input('data') !== 'null' && $request->input('data') !== 'undefined') {
                $query = $query->where("title", 'like', '%' . $request->input('data') . '%');
            }
            $product=$query->orderBy('product_id','desc')->paginate(10);
            return response()->json([
                'status'=>true,
                'data'=>$product
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
        try{
            $disPath = public_path();
            $product = new Product();
            $filePath = '';
            if ( $request->picture != null ) {

                $DIR = $disPath.'\uploads\product';
                $httpPost = file_get_contents( 'php://input' );
                $file_chunks = explode( ';base64,', $request->picture[ 0 ] );
                $fileType = explode( 'image/', $file_chunks[ 0 ] );
                $image_type = $fileType[ 0 ];

                //return response()->json( $file_chunks );
                $base64Img = base64_decode( $file_chunks[ 1 ] );
                $data = iconv( 'latin5', 'utf-8', $base64Img );
                $name = uniqid();
                $file = $DIR .'\\'. $name . '.png';
                $filePath = 'product/'.$name . '.png';

                file_put_contents( $file,  $base64Img );
            }
            $product-> title = $request->title;
            $product-> picture = $filePath;
            $product->friendly_url= $request->link?$request->link:'#';
            $product-> description = $request->description?$request->description:0;
            $product-> display = $request->display;
            $product->save();
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
        try{
            $list =Product::where('product_id',$id)->first();
            return response()->json([
                'status'=> true,
                'data' => $list
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
            $disPath = public_path();
            $product =Product::where('product_id',$id)->first();

            $filePath = '';
            if ($request->picture != null && $request->picture != $product->picture) {

                $DIR = $disPath . '\uploads\product';
                $httpPost = file_get_contents('php://input');
                $file_chunks = explode(';base64,', $request->picture[0]);
                $fileType = explode('image/', $file_chunks[0]);
                $image_type = $fileType[0];

                $base64Img = base64_decode($file_chunks[1]);
                $data = iconv('latin5', 'utf-8', $base64Img);
                $name = uniqid();
                $file = $DIR . '\\' . $name . '.png';
                $filePath = 'product/' . $name . '.png';

                file_put_contents($file,  $base64Img);
            } else {
                $filePath =  $product-> picture;
            }
            $product-> title = $request->title;
            $product-> picture = $filePath;
            $product->friendly_url= $request->link?$request->link:'#';
            $product-> description = $request->description?$request->description:0;
            $product-> display = $request->display;
            $product->save();
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $list =Product::where('product_id',$id)->first();
            if($list){
                $list->delete();
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
    public function deleteAll(Request $request)
    {
        $arr =$request->data;
        try {

                if($arr)
                {
                    foreach ($arr as $item) {
                        Product::where('product_id',$item)->first()->delete();
                    }
                }
                else
                {
                    return response()->json([
                    'status'=>false,
                    ],422);
                }
                return response()->json([
                    'status'=>true,
                ],200);
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
