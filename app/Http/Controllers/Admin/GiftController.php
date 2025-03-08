<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gift;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GiftController extends Controller
{

    public function index()
    {
        $gift = Gift::all()->makeHidden(['created_at', 'updated_at']);
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $gift
        ], 200);
    }

    public function create() {}

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'reward_point' => 'required|integer',
                'picture' => 'nullable',
                'display' => 'required|boolean',
            ]);

            $gift = new Gift();
            $gift->title = $validatedData['title'];
            $gift->description = $validatedData['description'] ?? null;
            $gift->reward_point = $validatedData['reward_point'];
            $gift->display = $validatedData['display'];

            // $filePath = '';
            // $disPath = public_path();
            // if ($request->picture != null && $gift->picture !=  $request->picture) {
            //     $DIR = $disPath . '\uploads\gift';
            //     $httpPost = file_get_contents('php://input');
            //     $file_chunks = explode(';base64,', $request->picture[0]);
            //     $fileType = explode('image/', $file_chunks[0]);
            //     $image_type = $fileType[0];
            //     $base64Img = base64_decode($file_chunks[1]);
            //     $data = iconv('latin5', 'utf-8', $base64Img);
            //     $name = uniqid();
            //     $file = $DIR . '\\' . $name . '.png';
            //     $filePath = 'gift/' . $name . '.png';
            //     file_put_contents($file,  $base64Img);
            // } else {
            //     $filePath =  $gift->picture;
            // }
            //$gift->picture = $filePath;
            $gift->save();

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $gift
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id) {}


    public function edit(string $id)
    {
        $gift = Gift::find($id)->makeHidden(['created_at', 'updated_at']);
        if ($gift == null) {
            return response()->json([
                'status' => false,
                'message' => 'Gift not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $gift
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        try {
            $gift = Gift::find($id);
            if ($gift == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gift not found'
                ], 404);
            }

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'reward_point' => 'required|integer',
                'picture' => 'nullable',
                'display' => 'required|boolean',
            ]);

            $gift->title = $validatedData['title'];
            $gift->description = $validatedData['description'] ?? null;
            $gift->reward_point = $validatedData['reward_point'];
            $gift->display = $validatedData['display'];

            // $filePath = '';
            // $disPath = public_path();
            // if ($request->picture != null && $gift->picture !=  $request->picture) {
            //     $DIR = $disPath . '\uploads\gift';
            //     $httpPost = file_get_contents('php://input');
            //     $file_chunks = explode(';base64,', $request->picture[0]);
            //     $fileType = explode('image/', $file_chunks[0]);
            //     $image_type = $fileType[0];
            //     $base64Img = base64_decode($file_chunks[1]);
            //     $data = iconv('latin5', 'utf-8', $base64Img);
            //     $name = uniqid();
            //     $file = $DIR . '\\' . $name . '.png';
            //     $filePath = 'theory/' . $name . '.png';
            //     file_put_contents($file,  $base64Img);
            // } else {
            //     $filePath =  $gift->picture;
            // }
            //$gift->picture = $filePath;
            $gift->save();

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $gift
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id) {}
}
