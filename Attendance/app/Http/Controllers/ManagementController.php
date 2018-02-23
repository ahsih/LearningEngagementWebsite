<?php

namespace attendance\Http\Controllers;

use attendance\declineModules;
use attendance\requestModule;
use attendance\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Input;

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
        //init all the approve request
        $listApprovedModules = array();

        //Check if it tutor requesting for this page
        if ($request->user()->hasRole('tutor')) {
            $permission = true;

            //get the tutor user,
            //so that we can find out what modules this tutor teaches and what module he can approve based
            // on what he teaches
            $listApprovedModules = $this->getTutorValidRequestModules($request);

        } else {
            $permission = false;
        }

        $data = array(
            'title' => 'Management',
            'permission' => $permission,
            'listApprovedModules' => $listApprovedModules
        );

        return view('pages.managementPage')->with($data);
    }

    /**
     * @param Request $request - request from the user
     * Get all the request modules from student depending on which modules the tutor teaches
     * @return list of modules which the tutor teaches
     */
    public function getTutorValidRequestModules(Request $request)
    {

        $listApprovedModules = array();
        $listOfTutorModules = array();

        $user = User::find($request->user()->id);

        foreach ($user->modules()->get() as $module) {
            array_push($listOfTutorModules, $module->id);
        }

        $allRequestModules = requestModule::all();
        foreach ($allRequestModules as $requestModules) {
            //If request module is same as what the tutor teaches, then this information should be stored into new array
            foreach ($listOfTutorModules as $value) {
                if ($requestModules->module_id == $value) {
                    //add the module to the array
                    array_push($listApprovedModules, $requestModules);
                }
            }
        }

        return $listApprovedModules;
    }

    /**
     * Accept the request module from the tutor.
     * @param Request $request - request from the user to the form
     * @return the management page
     */
    public function acceptRequest(Request $request)
    {
        $listApprovedModules = array();
        $listApprovedModules = $this->getTutorValidRequestModules($request);

        //Get all the radio button details
        //That user has been submitted
        foreach ($listApprovedModules as $module) {
            $userId = $module->user_id;
            $moduleId = $module->module_id;

            $fullRadioName = "userID" . $userId . "moduleID" . $moduleId;
            //get the radio button value, if it not selected, then it empty.
            $radioButton = Input::get($fullRadioName);
            if ($radioButton != null) {
                if ($radioButton == "accept") {
                    //Delete the request module
                    $this->deleteRequestModule($userId,$moduleId);

                    //Add the user into the user module
                    User::find($userId)->modules()->attach($moduleId);

                } else {
                    //Else it will be decline, so once we delete the request module, we need to alert the student
                    //Delete the request module
                    $this->deleteRequestModule($userId,$moduleId);

                    //Add to the decline list
                    $declineModule = new declineModules();
                    $declineModule->module_name = $module->module_name;
                    $declineModule->user_id = $userId;
                    $declineModule->timestamps = false;
                    $declineModule->save();
                }
            }
        }


        return redirect('/management');
    }

    /**
     * Delete the request module according to the user id and module id
     * @param $userId
     * @param $moduleId
     */
    public function deleteRequestModule($userId,$moduleId){
        requestModule::where('user_id', '=', $userId)
            ->where('module_id', '=', $moduleId)->delete();
    }


}
