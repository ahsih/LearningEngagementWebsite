<?php

namespace attendance\Http\Controllers;

use attendance\declineModules;
use attendance\FirstChoiceUserModule;
use attendance\Module;
use attendance\question;
use attendance\requestModule;
use attendance\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Input;

class PollingController extends Controller
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
     * Show the application polling homepage
     * @param $request - request from the user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //Get the path
        $path = $request->path();

        //Store the detail that will pass to the view
        $data = array(
            'path' => $path,
            'title' => 'Classroom polling'
        );
        return view('pages.pollingPage')->with($data);
    }

    /**
     * @param Request $request
     * Create a polling for tutor to create a question
     */
    public function createPoll(Request $request){
        $post = Input::all();
            //Get the main question first
                $mainQuestion = Input::get('mainQuestion');
                //Get list of optional answers
                $optionalAnswers = Input::get('optionalAnswers');

                //Create new question
                $question = new question();
    }
}
