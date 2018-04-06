<?php

namespace attendance\Http\Controllers;

use attendance\Award;
use attendance\Conversation;
use attendance\declineModules;
use attendance\FirstChoiceUserModule;
use attendance\Lesson;
use attendance\ActiveLesson;
use attendance\LoginTime;
use attendance\Module;
use attendance\question;
use attendance\requestModule;
use attendance\Response;
use attendance\Reward;
use attendance\RewardAchieve;
use Carbon\Carbon;
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
     * Set the user to the login if it's necessary
     */
    private function setUserToLogin($userID)
    {

        //Get the user detail
        $user = User::find($userID);

        if ($user->hasRole('student')) {
            if (!session()->has('loginTime')) {

                $loginTime = LoginTime::where('user_id', '=', $user->id)->first();

                if ($loginTime == null) {
                    //Add the user to the login time
                    $loginTime = new LoginTime();
                    $loginTime->user_id = $userID;
                    $loginTime->login_time = Carbon::now();
                    $loginTime->logout = false;
                    $loginTime->timestamps = false;
                    $loginTime->save();
                } else {
                    $loginTime->login_time = Carbon::now();
                    $loginTime->logout = false;
                    $loginTime->timestamps = false;
                    $loginTime->save();
                }

                //Create a session that user has log into the application
                session(['loginTime' => 'logged']);
            }
        }


    }

    /**
     * Show the application homepage
     * @param $request - the request from user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //get the user ID
        $user_id = $request->user()->id;

        $this->setUserToLogin($user_id);

        //Get the module this user teaches/studies
        $modules = User::find($user_id)->modules;

        //Get the path
        $path = $request->path();

        //Get all the modules users don't have
        $notAvailableModules = $this->getNotAvailableModules($modules);

        //Get a list of decline module
        $listOfDeclineModules = declineModules::where('user_id', '=', $user_id)->get();

        //Get the first choice of the module the student/tutor pick
        $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user_id)->first();

        //if user don't have first choice module, then we should cancel it.
        //Get the conversation chat
        if ($firstChoiceModule == null) {
            $conversations = null;
            $moduleName = null;
            $lessons = null;
            $activeLesson = null;
            $questions = null;
            $rewardList = null;
            $rewardPoint = null;
            $award = null;
            $listUsersInThisModule = null;

        } else {
            //Get a list of message from this module
            $conversations = Conversation::orderBy('created_at')
                ->where('module_id', '=', $firstChoiceModule->module_id)->get();

            //Get the module name
            $module = Module::find($firstChoiceModule->module_id);
            $moduleName = $module->module_name;

            //Get a list of lesson from this modules
            $lessons = Lesson::where('module_id', '=', $firstChoiceModule->module_id)->get();

            //Check if there is currently a lesson ongoing for polling
            $activeLesson = ActiveLesson::where('module_id', '=', $firstChoiceModule->module_id)->first();
            //Get all the questions from this active lesson
            $questions = $this->getQuestions($activeLesson);

            //Get a list of the request module
            $managementController = new ManagementController();

            //Get list of reward
            $rewardList = Reward::where('module_id', '=', $firstChoiceModule->module_id)->get();

            //Get this user reward achieve
            $rewardAchieve = RewardAchieve::where('user_id', '=', $user_id)->where('module_id', '=', $firstChoiceModule->module_id)->first();

            //Get the amount of reward achieve for this user.
            if ($rewardAchieve != null) {
                $rewardPoint = $rewardAchieve->amount;
            } else {
                $rewardPoint = 0;
            }

            //Get list of users in this module
            $listUsersInThisModule = Module::find($firstChoiceModule->module_id)->users;

            //Get list of rewards from this user
            $award = Award::where('user_id', '=', $user_id)->where('module_id', '=', $firstChoiceModule->module_id)->get();

        }

        //Return two different views for students and tutors
        if ($request->user()->hasRole('student')) {

            //Pass to the view
            $data = array(
                'rewardPoint' => $rewardPoint,
                'rewardList' => $rewardList,
                'activeLesson' => $activeLesson,
                'questions' => $questions,
                'path' => $path,
                'allModules' => $notAvailableModules,
                'modules' => $modules,
                'conversations' => $conversations,
                'moduleName' => $moduleName,
                'listDeclineModules' => $listOfDeclineModules,
                'role' => 'student',
                'award' => $award,
            );

            return view('pages.studentHome')->with($data);
        } else if ($request->user()->hasRole('tutor')) {

            //List of the approve modules by tutor
            $listOfApprovedModules = $managementController->getTutorValidRequestModules($request);
            session(['requestModulesCount' => sizeof($listOfApprovedModules)]);

            //Pass to the view
            $data = array(
                'firstChoiceModule' => $firstChoiceModule,
                'listUsersInThisModule' => $listUsersInThisModule,
                'activeLesson' => $activeLesson,
                'lessons' => $lessons,
                'path' => $path,
                'allModules' => $notAvailableModules,
                'modules' => $modules,
                'conversations' => $conversations,
                'moduleName' => $moduleName,
                'listDeclineModules' => $listOfDeclineModules,
                'role' => 'tutor'
            );

            //Pass all the modules this tutor teaches
            return view('pages.tutorHomePage')->with($data);

        } else if ($request->user()->hasRole('admin')) {

            //Get a list of students user
            $studentUsers = $this->getAllStudentUser();
            //Get a list of tutors user
            $tutorUsers = $this->getAllTutorUser();

            $data = array(
                'title' => 'Admin Page',
                'path' => $path,
                'tutorUsers' => $tutorUsers,
                'studentUsers' => $studentUsers,
            );

            return view('pages.adminPage')->with($data);
        }
    }

    /**
     * Get a list of questions from active lesson
     * @param $activeLesson
     * @return null
     */
    private function getQuestions($activeLesson)
    {
        //Get the question that are related to this module
        if ($activeLesson != null) {
            $questions = question::where('lesson_id', '=', $activeLesson->lesson_id)->get();
        } else {
            $questions = null;
        }


        return $questions;
    }

    /**
     * Return all users which are tutor
     * @return array
     */
    private function getAllTutorUser()
    {
        //Get all the users in the database
        $users = User::all();
        $tutorUsers = array();
        foreach ($users as $user) {
            if ($user->hasRole('tutor')) {
                array_push($tutorUsers, $user);
            }
        }

        return $tutorUsers;
    }

    /**
     * Get all the student user array
     * @return a list of students
     */
    private function getAllStudentUser()
    {
        //Get all the users in the database
        $users = User::all();
        $studentUsers = array();
        foreach ($users as $user) {
            if ($user->hasRole('student')) {
                array_push($studentUsers, $user);
            }
        }

        return $studentUsers;
    }

    /**
     * Get all the not available modules from the user
     * @param $modules - list of all modules
     * @return not available modules
     */
    private
    function getNotAvailableModules($modules)
    {
        //Get a list of all the modules
        $allModules = Module::all();
        $keyLocation = array();

        //Get only the module the users don't have
        foreach ($modules as $module) {
            for ($i = 0; $i < sizeof($allModules); $i++) {
                if ($module->id == $allModules[$i]->id) {
                    array_push($keyLocation, $i);
                }
            }
        }

        foreach ($keyLocation as $key) {
            unset($allModules[$key]);
        }

        $allModules = $allModules->values();


        return $allModules;
    }

}
