<?php

namespace attendance\Http\Controllers;

use attendance\User;
use Illuminate\Http\Request;
use Auth;

class RewardController extends Controller
{
    /**
     * Create a new controller instance.
     * Return to login screen , if it is not auth
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application management homepage
     * @param $request - request from the user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Get the user detail
        $user = User::find($request->user()->id);

        //Get the path
        $path = $request->path();


        $data = array(
            'path' => $path,
            'title' => 'Reward'
        );

        if($user->hasRole('tutor')){
            return view('pages.rewardPage-tutor')->with($data);
        }



    }
}
