<?php

namespace App\Http\Controllers;

use App\Models\Shift;
#use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $shifts=Shift::all();
        return response()->json($shifts);
    }

    /**
     * Display a listing of one resource by id.
     */
    public function indexById($id)
    {
        $shift = Shift::find($id);

        if (!$shift) {
            return response()->json(['message' => 'Shift not found.'], 404);
        }
    
        return response()->json($shift);
    }


    /**
     * Display a listing of the resource filtered by an specific id.
     */
    public function indexMS($movieId)
    {
        //
        $shifts = Shift::where('movie_id', $movieId)->get();
        return response()->json($shifts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeMS($movieId,StoreShiftRequest $request)
    {
        //
        $request->validate([
            'date' => 'required',
            'movie_id' => 'required'
        ]);

        $shift= new Shift();

        $shift->movie_id = $movieId;

        $formattedDate = date('Y-m-d H:i:s', strtotime($request->status));
        $shift->date = $formattedDate;

        $shift->status = $request->status;

        $shift->save();

        return response()->json($shift);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShiftRequest $request)
    {
        //
        $request->validate([
            'date' => 'required',
            'movie_id' => 'required'
        ]);

        $shift= new Shift();

        $shift->movie_id = $request->movie_id;

        $formattedDate = date('Y-m-d H:i:s', strtotime($request->status));
        $shift->date = $formattedDate;

        $shift->status = $request->status;

        $shift->save();

        return response()->json($shift);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShiftRequest $request, Shift $shift)
    {
        //
        $shift = Shift::findOrFail($request->id);

        $shift->movie_id = $request->movie_id;

        $formattedDate = date('Y-m-d H:i:s', strtotime($request->status));
        $shift->date = $formattedDate;
        
        $shift->status = $request->status;

        $shift->save();

        return response()->json($shift);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $shift = Shift::destroy($request->id);
        return ($shift);
    }
}
