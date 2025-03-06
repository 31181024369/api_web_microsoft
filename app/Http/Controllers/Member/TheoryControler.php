<?php

namespace App\Http\Controllers\Member;

use App\Models\TheOryCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TheoryControler extends Controller
{

    public function index()
    {
        try {
            $theOryCategory = TheOryCategory::all();
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


    public function create() {}


    public function store(Request $request) {}

    public function show(string $id) {}


    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
