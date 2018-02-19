<?php

namespace attendance\Http\Controllers;

use attendance\User;
use Illuminate\Http\Request;
use Auth;

class ManagementController extends Controller
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

        //Check if it tutor requesting for this page
        if ($request->user()->hasRole('tutor')) {
            $permission = true;
            //get the tutor user,
            //so that we can find out what modules this tutor teaches and what module he can approve based
            // on what he teaches
            $user = User::find($request->user()->id);

        } else {
            $permission = false;
        }

        $data = array(
            'title' => 'Management',
            'permission' => $permission
        );

        return view('pages.managementPage')->with($data);
    }
}
