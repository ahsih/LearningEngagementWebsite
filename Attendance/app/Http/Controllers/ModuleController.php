<?php

namespace attendance\Http\Controllers;

use attendance\FirstChoiceUserModule;
use attendance\requestModule;
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
     * select module into the user
     */
    public function selectModule()
    {
        //Get the request ID
        $moduleID = request()->moduleID;

        //Get the user details
        //Attach the module ID to the user
        $user = User::find(Auth::user()->id);
        $module = Module::find($moduleID);
        //if user do not contains this module
        if (!$user->hasModule($moduleID)) {

            //If it a student, then he has to request to join the module
            if ($user->hasRole('student')) {
                //Check if it already exist in the table
                $requestAlreadyExist = requestModule::where('user_id', '=', $user->id)
                    ->where('module_id', '=', $moduleID)->exists();

                if ($requestAlreadyExist) {
                    return "requestAlreadyMade";
                } else {
                    //Add new request
                    $newRequest = new requestModule();
                    $newRequest->user_id = $user->id;
                    $newRequest->module_id = $moduleID;
                    $newRequest->full_name = $user->name;
                    $newRequest->email = $user->email;
                    $newRequest->module_name = $module->module_name;
                    $newRequest->timestamps = false;
                    $newRequest->save();

                    return "requestAdded";
                }
                
                //If it a tutor
            } else {
                $user->modules()->attach($moduleID);

                //Get first choice of the user, if user doesn't have a first choice of the module,
                //Then he will set this module as first choice
                $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user->id)->exists();
                //If it null
                if (!$firstChoiceModule) {
                    $firstChoiceModule = new FirstChoiceUserModule();
                    $firstChoiceModule->user_id = $user->id;
                    $firstChoiceModule->module_id = $moduleID;
                    $firstChoiceModule->timestamps = false;
                    //Save first choice
                    $firstChoiceModule->save();
                }

                return "moduleAdded";
            }

        } else {

            return "false";
        }
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