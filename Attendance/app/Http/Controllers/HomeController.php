<?php

namespace attendance\Http\Controllers;

use attendance\Conversation;
use attendance\FirstChoiceUserModule;
use attendance\Module;
use Illuminate\Http\Request;
use Auth;
use attendance\User;

class HomeController extends Controller
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
        //get the user ID
       $user_id = $request->user()->id;
       //Get the module this user teaches/studies
      $modules = User::find($user_id)->modules;

      $firstChoiceModule = FirstChoiceUserModule::where('user_id','=',$user_id)->first();

      //Get the conversation chat
      $conversations = Conversation::orderBy('created_at')
          ->where('user_id','=',$user_id)
          ->where('module_id','=',$firstChoiceModule->module_id)->get();

      //Get the module name
        $moduleName = Module::find($firstChoiceModule->module_id);

      //Pass to the view
      $data = array(
          'modules' => $modules,
          'conversations' => $conversations,
          'moduleName' => $moduleName->module_name
      );

        if($request->user()->hasRole('student')){
            return view('pages.studentHome')->with($data);
        }else{
            //Pass all the modules this tutor teaches
            return view('pages.tutorHomePage')->with($data);
        }
            
        
    }
}
