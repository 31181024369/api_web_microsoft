<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    public function showUser(){
        try{
            $Product=Product::orderBy('product_id','desc')->take(7)->get();
            return response()->json([
                'status'=>true,
                'data'=>$Product
            ]);
        }catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
