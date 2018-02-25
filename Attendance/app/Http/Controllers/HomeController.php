<?php

namespace attendance\Http\Controllers;

use attendance\Conversation;
use attendance\declineModules;
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

        //Get a list of all the modules
        $allModules = Module::all();

        //Get a list of decline module
        $listOfDeclineModules = declineModules::where('user_id', '=', $user_id)->get();

        //Get the first choice of the module the student/tutor pick
        $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user_id)->first();

        //if user don't have first choice module, then we should cancel it.
        //Get the conversation chat
        if ($firstChoiceModule == null) {
            $conversations = null;
            $moduleName = null;

        } else {
            $conversations = Conversation::orderBy('created_at')
                ->where('module_id', '=', $firstChoiceModule->module_id)->get();

            //Get the module name
            $module = Module::find($firstChoiceModule->module_id);
            $moduleName = $module->module_name;
        }

        //Return two different views for students and tutors
        if ($request->user()->hasRole('student')) {

            //Pass to the view
            $data = array(
                'allModules' => $allModules,
                'modules' => $modules,
                'conversations' => $conversations,
                'moduleName' => $moduleName,
                'listDeclineModules' => $listOfDeclineModules,
                'role' => 'student'
            );

            return view('pages.studentHome')->with($data);
        } else {
            //Pass to the view
            $data = array(
                'allModules' => $allModules,
                'modules' => $modules,
                'conversations' => $conversations,
                'moduleName' => $moduleName,
                'listDeclineModules' => $listOfDeclineModules,
                'role' => 'tutor'
            );
            //Pass all the modules this tutor teaches
            return view('pages.tutorHomePage')->with($data);
        }
    }
}
