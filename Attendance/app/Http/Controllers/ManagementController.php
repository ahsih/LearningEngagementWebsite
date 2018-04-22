<?php

namespace attendance\Http\Controllers;

use attendance\declineModules;
use attendance\EmailRequestModule;
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
        //Forget the session of the count if exist.
        if(session()->has('requestModulesCount')){
            session()->forget('requestModulesCount');
        }

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

        //List of approve module
        $listApprovedModules = array();
        //List of the tutor
        $listOfTutorModules = array();

        $user = User::find($request->user()->id);

        //Get a list of module that this tutor has
        foreach ($user->modules()->get() as $module) {
            array_push($listOfTutorModules, $module->id);
        }

        //Loop all the request modules, then check if this tutor teaches.
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
        //Check how many been delete.
        $deleteAmount = 0;
        foreach ($users as $user) {
            //if user has this module
            if ($user->hasModule($firstChoice->module_id)) {
                //Get the checkbox detail
                $checkbox = Input::get($user->id);
                if ($checkbox == 'true') {
                    //Increment by 1
                    $deleteAmount++;
                    User::find($user->id)->modules()->detach($firstChoice->module_id);
                    $userFirstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user->id)->
                    where('module_id', '=', $firstChoice->module_id)->first();
                    if ($userFirstChoiceModule != null) {
                        $userFirstChoiceModule->delete();
                    }
                }
            }
        }

        if($deleteAmount == 0) {
            session(['managementError' => 'No student has been deleted']);
        }else{
            session(['managementSuccess' => 'Total ' . $deleteAmount . ' students has been deleted']);
        }

        //Return redirect to the management
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
        //Total students that has been added
        $addAmount = 0;
        foreach ($users as $user) {
            //If user do not have this module, then check whether you have select to add them to the module
            if ($user->hasRole('student')) {
                if (!$user->hasModule($firstChoice->module_id)) {
                    $checkbox = Input::get($user->id);
                    if ($checkbox == 'true') {
                        //Increment
                        $addAmount++;
                        //Add the user to the module
                        User::find($user->id)->modules()->attach($firstChoice->module_id);
                        $this->setUserFirstChoiceModule($user,$firstChoice->module_id);
                    }
                }
            }
        }

        if($addAmount == 0) {
            session(['managementError' => 'No student has been added']);
        }else{
            session(['managementSuccess' => 'Total ' . $addAmount . ' students has been added']);
        }


        return redirect('/management');
    }

    /**
     * Set the user first choice module
     * @param user - the current user
     * @param modueID - the module ID
     */
    private function setUserFirstChoiceModule($user,$moduleID){
        //Check if this user has a first choice module on their list
        $firstChoiceOfUser = FirstChoiceUserModule::where('user_id','=',$user->id)->first();
        if($firstChoiceOfUser == null){
            $firstChoiceModule = new FirstChoiceUserModule();
            $firstChoiceModule->user_id = $user->id;
            $firstChoiceModule->module_id = $moduleID;
            $firstChoiceModule->timestamps = false;
            //Save first choice
            $firstChoiceModule->save();
        }
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
     * Create tutor from a student profile
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
     * Change from tutor profile to student
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function changeToStudent(){

        //Get all the users
        //change from tutor profile to student profile
        $users = User::all();
        foreach ($users as $user) {
            if ($user->hasRole('tutor')) {
                $checkbox = Input::get($user->id);
                if ($checkbox == 'true') {
                    $tutorRole = Role::where('name', '=', 'tutor')->first();
                    $studentRole = Role::where('name', '=', 'student')->first();
                    $user->roles()->detach($tutorRole->id);
                    $user->roles()->attach($studentRole->id);
                }
            }
        }

        //Return to home page
        return redirect('/');
    }

    /**
     * Add a list of students from CSV file
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addListOfStudents(Request $request)
    {
        //By default if it didn't pass the CSV then the file is invalid
        session(['managementError' => 'The file you upload is invalid']);

        //Validation for CSV
        $this->validate($request, [
                'file' => 'required|mimes:csv,txt',
            ]
        );

        //Check if the file exist
        if (Input::has('file')) {
            //Forget the session
            session()->forget('fileError');
            //Get the moduleID
            $moduleID = FirstChoiceUserModule::where('user_id', '=', $request->user()->id)->first()->module_id;

            //Get the file
            $file = Input::file('file');

            //Read the CSV
            $listOfEmails = $this->readCSV($file);

            //Loop the list of emails
            if (sizeof($listOfEmails) > 0) {
                foreach ($listOfEmails as $email) {
                    //Add user to the module
                    $userExists = $this->addUserToModule($moduleID, $email);
                    //if user not exist, then we will store this email and the moduleID in the email request module model.
                    if (!$userExists) {
                        //If the email is not exist in the email request module yet, then we need to add it into the table
                        if (!EmailRequestModule::where('email', '=', $email)->exists()) {
                            $emailRequestModule = new EmailRequestModule();
                            $emailRequestModule->timestamps = false;
                            $emailRequestModule->email = $email;
                            $emailRequestModule->module_id = $moduleID;
                            $emailRequestModule->save();
                        }
                    }
                }
            }
            //Add file success
            session(['managementSuccess' => 'File added successfully']);
        }

        return redirect('/management');
    }

    /**
     * Add the user to the module
     * @param $moduleID
     * @param $email
     * @return bool - true if found and add, otherwise keep it.
     */
    private function addUserToModule($moduleID, $email)
    {
        //Check if email is exist in the user
        $user = User::where('email', '=', $email)->first();
        //If the user exist
        if ($user != null) {
            //Check user already contains this module
            $alreadyExist = $this->checkUserAlreadyContainsThisModule($moduleID, $user);
            if (!$alreadyExist) {
                $user->modules()->attach($moduleID);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if this user already contain in this module
     * @param $moduleID
     * @param $user
     * @return bool
     */
    private function checkUserAlreadyContainsThisModule($moduleID, $user)
    {
        //Get list of the modules this user has
        $userModules = $user->modules()->get();
        //Loop and check if user has already this module ID
        if ($userModules != null && sizeof($userModules) > 0) {
            foreach ($userModules as $module) {
                if ($module->id == $moduleID) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Read the CSV file
     * @param $file
     * @return mixed - an array contains list of email in this module
     */
    private function readCSV($file)
    {
        //Get handler
        $handle = fopen($file->getRealPath(), "r");
        //Find the indexOfEmail - by default is 0
        $indexEmail = 0;
        //Boolean to check if email is found
        $emailFound = false;
        //Create array
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
            //Filter email
            if (filter_var($data[$indexEmail], FILTER_VALIDATE_EMAIL)) {
                //Add the email into the array
                array_push($listOfEmails, $data[$indexEmail]);
            }
        }

        //Return list of emails
        return $listOfEmails;
    }
}
