<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Adpos;
use App\Models\Advertise;
class AdvertiseController extends Controller
{
    public function showAdvertise(Request $request){
        try{
            $advertise=Advertise::orderBy('id','asc')->where('display',1)->get();
            $adpos=Adpos::get();
            $data=[];
            foreach($advertise as $item){
                $adpos=Adpos::where('id_pos',$item->id_pos)->first();
                $data[]=[
                    'id'=>$item->id,
                    'title'=>$item->title,
                    'picture'=>$item->picture,
                    'width'=>$item->width,
                    'height'=>$item->height,
                    'link'=>$item->link,
                    'description'=>$item->description,
                    'display'=>$item->display,
                    'ad_pos'=>$adpos
                ];

            }
            return response()->json([
                'statis'=>true,
                'data'=>$data
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
