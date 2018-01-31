<?php

namespace attendance\Http\Controllers;

use attendance\FirstChoiceUserModule;
use Auth;
use attendance\User;
use attendance\Module;
use attendance\Conversation;

class ModuleController extends Controller
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
     * Add the module to the database
     * Getting the module name from the form
     */
    public function addModule()
    {
        //Get the request module
        $moduleName = request()->moduleName;

        if (!Module::where('module_name', '=', $moduleName)->exists()) {
            $module = new Module();
            $module->module_name = $moduleName;
            //Save the module
            $module->save();

            //Get the user details
            //Attach the module ID to the user
            $user = User::find(Auth::user()->id);
            $user->modules()->attach($module->id);

            //Get first choice of the user, if user doesn't have a first choice of the module,
            //Then he will set this module as first choice
            $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user->id)->exists();
            //If it null
            if (!$firstChoiceModule) {
                $firstChoiceModule = new FirstChoiceUserModule();
                $firstChoiceModule->user_id = $user->id;
                $firstChoiceModule->module_id = $module->id;
                $firstChoiceModule->timestamps = false;
                //Save first choice
                $firstChoiceModule->save();
            }

            //Create a new conversation
            //Start a conversation
            $conversation = new Conversation();
            $conversation->message = "Starting conversation for: " . $moduleName;
            $conversation->user_id = $user->id;
            $conversation->module_id = $module->id;
            //save the conversation
            $conversation->save();

            return "true";
        } else {
            return "false";
        }
    }


}