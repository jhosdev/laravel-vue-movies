<?php

namespace App\Http\Controllers;

use App\Models\Movie;
#use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies=Movie::all();
        return response()->json($movies);
    }

    /**
     * Display a listing of one resource by id.
     */
    public function indexById($id)
    {
        $movie = Movie::find($id);

        if (!$movie) {
            return response()->json(['message' => 'Movie not found.'], 404);
        }
    
        return response()->json($movie);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovieRequest $request)
    {
        //
        $request->validate([
            'name' => 'required'
        ]);

        $movie = new Movie();

        $movie->name = $request->name;

        $formattedDate = date('Y-m-d H:i:s', strtotime($request->date));
        $movie->date = $formattedDate;

        $movie->status = $request->status;
        $movie->imgPath = $request->imgPath;

        $movie->save();

        return response()->json($movie);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovieRequest $request, Movie $movie)
    {
        //
        $movie = Movie::findOrFail($request->id);

        $movie->name = $request->name;

        $formattedDate = date('Y-m-d H:i:s', strtotime($request->date));
        $movie->date = $formattedDate;

        $movie->status = $request->status;
        $movie->imgPath = $request->imgPath;

        $movie->save();

        return response()->json($movie);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $movie = Movie::destroy($request->id);
        return ($movie);
    }
}
