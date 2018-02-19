<?php

namespace attendance\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class LiveChatController extends Controller
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
     * Show the application homepage
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = array(
            'title'=> 'Live Chat'
        );

        return view('pages.liveChatPage')->with($data);
    }
}
