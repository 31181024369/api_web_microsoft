<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\TheOryCategory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class TheOryCategoryController extends Controller
{

    public function index(Request $request)
    {
        try {
            // $now = date('d-m-Y H:i:s');
            // $stringTime = strtotime($now);
            // DB::table('adminlogs')->insert([
            //     'admin_id' => Auth::guard('admin')->user()->id,
            //     'time' =>  $stringTime,
            //     'ip' => $request->ip(),
            //     'action' => 'show all newsCategory',
            //     'cat' => 'newsCategory',
            // ]);

            if ($request->data == 'undefined' || $request->data == "") {
                $theOryCategory = TheOryCategory::all();
            } else {
                $theOryCategory = TheOryCategory::where('title', 'like', '%' . $request->data . '%')->get();
            }
            $response = [
                'status' => true,
                'list' => $theOryCategory,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $response = [
                'status' => 'false',
                'error' => $errorMessage
            ];
            return response()->json($response, 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'friendly_url' => 'required|string|max:255',
            'parentid' => 'required|integer',
            'display' => 'required|boolean',
        ]);

        try {
            $theOryCategory = new TheOryCategory();
            $theOryCategory->title = $request->input('title');
            $theOryCategory->description = $request->input('description');
            $theOryCategory->friendly_url = $request->input('friendly_url');
            $theOryCategory->parentid = $request->input('parentid');
            $theOryCategory->display = $request->input('display');
            $theOryCategory->save();

            $response = [
                'status' => true,
                'theOryCategory' => $theOryCategory,
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $response = [
                'status' => false,
                'error' => $errorMessage
            ];
            return response()->json($response, 500);
        }
    }

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
