<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gift;

class GiftController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = Gift::query();
            $perPage = $request->input('per_page', 10);

            $gifts = $query->orderBy('id', 'desc')
                ->paginate($perPage)
                ->makeHidden(['created_at', 'updated_at']);

            $response = [
                'status' => true,
                'list' => $gifts->items(),
                'pagination' => [
                    'current_page' => $gifts->currentPage(),
                    'total_pages' => $gifts->lastPage(),
                    'per_page' => $gifts->perPage(),
                    'total' => $gifts->total(),
                ],
            ];

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
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
