<?php

namespace attendance\Http\Controllers;

use attendance\Conversation;
use attendance\declineModules;
use attendance\FirstChoiceUserModule;
use attendance\Lesson;
use attendance\ActiveLesson;
use attendance\Module;
use attendance\question;
use attendance\Response;
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
     * @param $request - the request from user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //get the user ID
        $user_id = $request->user()->id;
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
            $lessonPointer = null;

        } else {
            $conversations = Conversation::orderBy('created_at')
                ->where('module_id', '=', $firstChoiceModule->module_id)->get();

            //Get the module name
            $module = Module::find($firstChoiceModule->module_id);
            $moduleName = $module->module_name;

            //Get a list of lesson from this modules
            $lessons = Lesson::where('module_id', '=', $firstChoiceModule->module_id)->get();

            //Check if there is currently a lesson ongoing for polling
            $lessonPointer = ActiveLesson::where('module_id', '=', $firstChoiceModule->module_id)->first();

            $questions = $this->getNotFilledQuestions($user_id, $lessonPointer);

        }

        //Return two different views for students and tutors
        if ($request->user()->hasRole('student')) {

            //Pass to the view
            $data = array(
                'lessonPointer' => $lessonPointer,
                'questions' => $questions,
                'path' => $path,
                'allModules' => $notAvailableModules,
                'modules' => $modules,
                'conversations' => $conversations,
                'moduleName' => $moduleName,
                'listDeclineModules' => $listOfDeclineModules,
                'role' => 'student'
            );

            return view('pages.studentHome')->with($data);
        } else if ($request->user()->hasRole('tutor')) {


            //Pass to the view
            $data = array(
                'lessonPointer' => $lessonPointer,
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

            $studentUsers = $this->getAllStudentUser();

            $data = array(
                'title' => 'Admin Page',
                'path' => $path,
                'studentUsers' => $studentUsers,
            );

            return view('pages.adminPage')->with($data);
        }
    }

    /**
     * Get the questions that are not filled
     * @param $user_id
     * @param $lessonPointer
     * @return the answer
     */
    private function getNotFilledQuestions($user_id, $lessonPointer)
    {
        //Get the question that are related to this module
        if ($lessonPointer != null) {
            $questions = question::where('lesson_id', '=', $lessonPointer->lesson_id)->get();
            //Get all the responses from this user
            $responses = Response::where('user_id', '=', $user_id)->get();

        //Key location of the array
        $keyLocation = array();

        //Loop the response
        //Check if the response has already got the question id
        //if it is, remove the question from the question array
        if ($questions != null && sizeof($questions) > 0) {
            if ($responses != null) {
                foreach ($responses as $response) {
                    for ($i = 0; $i < sizeof($questions); $i++) {
                        if ($response->question_id == $questions[$i]->id) {
                            array_push($keyLocation, $i);
                        }
                    }
                }
            }
        }

        //Unset the location
        foreach ($keyLocation as $key) {
            unset($questions[$key]);
        }

        //Reindex the questions
        $questions = $questions->values();
        }else{
            $questions = null;
        }


        return $questions;
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
