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
            // $advertise=Advertise::where('display',1)->get()->groupBy('id_pos');


            $advertise = Advertise::with('Adpos')
                ->where('display', 1)
                ->get()
                ->groupBy(function ($item) {
                    return optional($item->Adpos)->name ?? optional($item->Adpos)->title;
                })
                ->map(function ($items) {
                    return $items->map(function ($item) {
                        unset($item->Adpos); // Xóa hoàn toàn thuộc tính 'Adpos'
                        return $item;
                    });
                });
            // return  $advertise;
            // $adpos=Adpos::get();
            // $data=[];
            // foreach($advertise as $item){
            //     $adpos=Adpos::where('id_pos',$item->id_pos)->first();
            //     $data[]=[
            //         'id'=>$item->id,
            //         'title'=>$item->title,
            //         'picture'=>$item->picture,
            //         'width'=>$item->width,
            //         'height'=>$item->height,
            //         'link'=>$item->link,
            //         'description'=>$item->description,
            //         'display'=>$item->display,
            //         'ad_pos'=>$adpos
            //     ];

            // }
            return response()->json([
                'status'=>true,
                'data'=> $advertise
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
