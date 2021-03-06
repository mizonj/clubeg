<?php

namespace App\Http\Controllers;

use App\Livescore;
use App\Tournament;
use App\User;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\Http\Requests;

class LivescoresController extends Controller
{

    public function index()
    {
        $scores = Livescore::all();

        return view('scores.index', ['scores' => $scores]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

        $score = new Livescore;

        return view('scores.create')->with(compact('score'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $score = new Livescore();
        $score->user_id = $request->user_id;
        $score->tournament_id = $request->tournament_id;
        $score->golfcourse_id = $request->golfcourse_id;
        $score->groupNo = $request->groupNo;
        $score->teamNo = $request->teamNo;
        $score->assignHoleValues($request);
        $score->save();

        if (Auth::user()) {
            Session::flash('alert-success', trans('scores.score_success_create'));
            return redirect('/scores/create');
        }
   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        // get the Score
        $score = Livescore::find($id);
        $users = User::all();

        // show the edit form and pass the Score
        return view('scores.edit', ['score' => $score, 'users' => $users]);
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return Response
     */

    public function update($id)
    {
        $request = request();
        $score = Livescore::findOrFail($id);
        $score->user_id = $request->user_id;
        $score->tournament_id = $request->tournament_id;
        $score->golfcourse_id = $request->golfcourse_id;
        $score->groupNo = $request->groupNo;
        $score->teamNo = $request->teamNo;
        $score->assignHoleValues($request);
        $score->save();

        if (Auth::user()) {
            Session::flash('alert-success', trans('scores.score_success_update'));
            return redirect('listtournaments');
        }

    }


    public function listtournaments()
    {
        $tournaments = Tournament::select(DB::raw("CONCAT(name) AS t_name, id"))->pluck('t_name', 'id');
        return view('scores.listtournaments', ['tournaments' => $tournaments]);
    }

    public function listscores($id)
    {
        $tournament = Tournament::find($id);
        $scores = $tournament->livescores;
        if (count($scores)) {
            $golfcourse = $scores[0]->golfcourse;
            return view('scores.listscores', ['scores' => $scores, 'golfcourse' => $golfcourse, 'tournament' => $tournament]);
        } else {
            Session::flash('alert-success', trans('scores.no_scores'));
            return redirect('/listtournaments');
        }
    }

     /**
     * Destroy the resource.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $score = Livescore::findOrFail($id);
        $score->delete();
        Session::flash('alert-success', trans('scores.score_success_delete'));
        return redirect('/scores');
    }
}