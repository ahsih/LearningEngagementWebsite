<?php

namespace attendance\Http\Controllers;

use attendance\declineModules;
use attendance\FirstChoiceUserModule;
use attendance\Module;
use attendance\requestModule;
use attendance\Role;
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
     * Show the application management homepage
     * @param $request - request from the user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //init all the approve request
        $listApprovedModules = array();

        //Get the path
        $path = $request->path();

        //Check if it tutor requesting for this page
        if ($request->user()->hasRole('tutor')) {
            $permission = true;

            //get the tutor user,
            //so that we can find out what modules this tutor teaches and what module he can approve based
            // on what he teaches
            $listApprovedModules = $this->getTutorValidRequestModules($request);

            //Get the module this tutor teaches
            $modules = User::find($request->user()->id)->modules;
            $firstChoice = FirstChoiceUserModule::where('user_id', '=', $request->user()->id)->first();
            $module_name = Module::find($firstChoice->module_id)->module_name;

            $listUsers = User::all();
            //Store all the students who's are not in this module
            $listNotInModuleStudentUsers = array();
            //Store all the students who in this module
            $listStudentInThisModule = array();
            //If user is a student , then we need to check whether he is already in the module
            foreach ($listUsers as $user) {
                if ($user->hasRole('student')) {
                    //If user do not have this module
                    if (!$user->hasModule($firstChoice->module_id)) {
                        array_push($listNotInModuleStudentUsers, $user);
                    } else {
                        array_push($listStudentInThisModule, $user);
                    }
                }
            }


        } else {
            //Student profile should not have access to this page
            $permission = false;
            $modules = null;
            $module_name = null;
            $listNotInModuleStudentUsers = null;
            $listStudentInThisModule = null;
        }

        $data = array(
            'path' => $path,
            'title' => 'Management',
            'permission' => $permission,
            'listApprovedModules' => $listApprovedModules,
            'modules' => $modules,
            'moduleName' => $module_name,
            'listNotInModuleStudentUsers' => $listNotInModuleStudentUsers,
            'listStudentInThisModule' => $listStudentInThisModule
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
                    $this->deleteRequestModule($userId, $moduleId);

                    //Add the user into the user module
                    User::find($userId)->modules()->attach($moduleId);

                } else {
                    //Else it will be decline, so once we delete the request module, we need to alert the student
                    //Delete the request module
                    $this->deleteRequestModule($userId, $moduleId);

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
     * Delete the student from this module
     * @return management page.
     */
    public function deleteStudentInModule()
    {

        //Get the module that is currently being managed
        $firstChoice = FirstChoiceUserModule::where('user_id', '=', Auth::user()->id)->first();
        $users = User::all();
        foreach ($users as $user) {
            //if user has this module
            if ($user->hasModule($firstChoice->module_id)) {
                //Get the checkbox detail
                $checkbox = Input::get($user->id);
                if ($checkbox == 'true') {
                    User::find($user->id)->modules()->detach($firstChoice->module_id);
                    $userFirstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user->id)->
                    where('module_id', '=', $firstChoice->module_id)->first();
                    if ($userFirstChoiceModule != null) {
                        $userFirstChoiceModule->delete();
                    }
                }
            }
        }

        return redirect('/management');
    }

    /**
     * Add the student to the module
     * @return the management page
     */
    public function addStudentToModule()
    {
        //Get the module that is currently being managed
        $firstChoice = FirstChoiceUserModule::where('user_id', '=', Auth::user()->id)->first();
        $users = User::all();
        foreach ($users as $user) {
            //If user do not have this module, then check whether you have select to add them to the module
            if ($user->hasRole('student')) {
                if (!$user->hasModule($firstChoice->module_id)) {
                    $checkbox = Input::get($user->id);
                    if ($checkbox == 'true') {
                        //Add the user to the module
                        User::find($user->id)->modules()->attach($firstChoice->module_id);
                    }
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
    public function deleteRequestModule($userId, $moduleId)
    {
        requestModule::where('user_id', '=', $userId)
            ->where('module_id', '=', $moduleId)->delete();
    }

    /**
     * Create tutor
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createTutor()
    {
        //Get all the users
        $users = User::all();
        foreach ($users as $user) {
            if ($user->hasRole('student')) {
                $checkbox = Input::get($user->id);
                if ($checkbox == 'true') {
                    $studentRole = Role::where('name', '=', 'student')->first();
                    $tutorRole = Role::where('name', '=', 'tutor')->first();
                    $user->roles()->detach($studentRole->id);
                    $user->roles()->attach($tutorRole->id);
                }
            }
        }

        return redirect('/');
    }

    /**
     * Add a list of students from CSV file
     */
    public function addListOfStudents(Request $request)
    {
        //By default if it didn't pass the CSV then the file is invalid
        session(['fileError' => 'The file you upload is invalid']);

        //Validation for CSV
        $this->validate($request, [
                'file' => 'required|mimes:csv,txt',
            ]
        );

        //Check if the file exist
        if (Input::has('file')) {
            //Get the file
            $file = Input::file('file');
            //Get handler
            $handle = fopen($file->getRealPath(), "r");

            //Find the indexOfEmail - by default is 0
            $indexEmail = 0;
            //Boolean to check if email is found
            $emailFound = false;
            //An array to store email
            $listOfEmails = array();

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                //Find the $data which contains email title
                if (!$emailFound) {
                    for ($i = 0; $i < sizeof($data); $i++) {
                        if (strpos($data[$i], 'Email') !== false) {
                            $indexEmail = $i;
                            $emailFound = true;
                        }
                    }
                }
                //Add the email into the array
                array_push($listOfEmails,$data[$indexEmail]);

            }
        }

        return redirect('/management');
    }
}
