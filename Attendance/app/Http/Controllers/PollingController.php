<?php

namespace attendance\Http\Controllers;

use attendance\FirstChoiceUserModule;
use attendance\Lesson;
use attendance\LessonPointer;
use attendance\Module;
use attendance\optionalAnswers;
use attendance\question;
use attendance\Response;
use attendance\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class PollingController extends Controller
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
     * Show the application polling homepage
     * @param $request - request from the user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Get the path
        $path = $request->path();

        //Get the student
        $user = User::find($request->user()->id);

        //Get the users module
        $modules = User::find($request->user()->id)->modules;

        //Get the total amount of lesson on first module in $modules
        //if there isn't any module, then we should put both total amount lesson and lesson to null
        if (sizeof($modules) > 0) {
            $totalAmountLesson = Lesson::where('module_id', '=', $modules[0]->id)->count();
            $lessons = Lesson::where('module_id', '=', $modules[0]->id)->get();
        } else {
            $totalAmountLesson = null;
            $lessons = null;
        }

        //Get a list of lesson from the first choice of the $modules
        //If there is session, then we need to change it.
        if (Session::has('moduleID')) {
            $lessons = Lesson::where('module_id', '=', Session::get('moduleID'))->get();
        }

        //Check if they are: student/tutor/admin
        if ($user->hasRole('student')) {

            $responses = Response::latest('created_at')
                ->where('user_id', '=', $user->id)->get();

            $data = array(
                'responses' => $responses,
                'title' => 'Classroom Polling',
                'path' => $path
            );

            return view('pages.pollingPage-student')->with($data);

        } else if ($user->hasRole('tutor')) {
            //Store the detail that will pass to the view
            $data = array(
                'lessons' => $lessons,
                'totalAmountLesson' => $totalAmountLesson,
                'role' => 'tutor',
                'modules' => $modules,
                'path' => $path,
                'title' => 'Classroom Polling'
            );

            return view('pages.pollingPage-tutor')->with($data);

        } else {
            //Store the detail that will pass to the view
            $data = array(
                'role' => 'admin',
                'modules' => $modules,
                'path' => $path,
                'title' => 'Classroom Polling'
            );
            return view('pages.pollingPage-tutor')->with($data);
        }
    }

    /**
     * Create a lesson
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createLesson()
    {
        $moduleID = Input::get('moduleListLesson');
        $totalAmountOfLesson = Input::get('hiddenAmountOfLesson');
        $lessonName = Input::get('lessonName');

        //Check if the lesson amount is same as from the table
        //Double validation
        $error = $this->checkLessonError($moduleID, $totalAmountOfLesson, $lessonName);

        if (empty($error)) {
            //Save the lesson
            $lesson = new Lesson();
            $lesson->module_id = $moduleID;
            $lesson->lesson_name = $lessonName;
            $lesson->save();

            //Created successfully
            session(['pollingSuccess' => 'Lesson has been created successfully on module: ' . Module::find($moduleID)->module_name]);
        } else {
            session(['pollingError' => $error]);
        }

        return redirect('/polling');
    }

    /**
     * Check if both module and lesson is fine.
     * @param $moduleID
     * @param $totalAmountOfLesson
     * @param $lessonName
     * @return $error
     */
    private function checkLessonError($moduleID, $totalAmountOfLesson, $lessonName)
    {
        $error = array();
        //Check if lesson name is not empty or space
        if ($lessonName == "" || ctype_space($lessonName)) {
            array_push($error, 'Lesson name is empty');
        }
        //Check if it a valid module
        $moduleCount = Module::find($moduleID)->count();
        if ($moduleCount == null) {
            array_push($error, 'Not a valid module');
        }
        //Check amount of lesson
        $amountLesson = Lesson::where('module_id', '=', $moduleID)->count();
        if ($amountLesson != $totalAmountOfLesson) {
            array_push($error, 'Amount of the lesson is not the same, someone has change the value');
        }

        return $error;
    }


    /**
     * Create a lesson pointer according to the lesson user has pick
     * @return back to the homepage once it has been picked
     */
    public function createLessonPointer()
    {

        //Get the lesson ID
        $lessonID = Input::get('firstModuleLessList');
        //Get the first choice of the module the student/tutor pick
        $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', Auth::user()->id)->first();
        //Get the lesson pointer
        $lessonPointer = LessonPointer::where('module_id', '=', $firstChoiceModule->module_id)->first();

        // If lesson ID is not empty
        if ($lessonID != null) {
            //If $lesson pointer is null
            if ($lessonPointer == null) {
                //Create a new pointer and store those data in.
                $lessonPointer = new LessonPointer();
                $lessonPointer->module_id = $firstChoiceModule->module_id;
                $lessonPointer->lesson_id = $lessonID;
                $lessonPointer->question_count = 0;
                //Set the end point
                $lessonPointer->end_point = false;
                //No timestamps
                $lessonPointer->timestamps = false;
                $lessonPointer->save();

            } else {
                //Update the current lesson pointer with new lesson
                $lessonPointer->lesson_id = $lessonID;
                //No timestamps
                $lessonPointer->timestamps = false;
                $lessonPointer->save();
            }
        }

        return redirect('/');
    }

    /**
     * When tutor press 'Next' on his classroom polling, it should display next question to the student.
     */
    public function nextLessonQuestion()
    {
        //Get the first choice of the module the student/tutor pick
        $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', Auth::user()->id)->first();
        //Get the lesson pointer
        $lessonPointer = LessonPointer::where('module_id', '=', $firstChoiceModule->module_id)->first();

        //add the size of the 1
        if ($lessonPointer != null) {
            $totalQuestions = $lessonPointer->lesson->questions->count();
            //increment 1 on question count in lesson pointer
            $this->addingQuestionCount($lessonPointer);
            //If the current lessonPointer is already the same as total amount of questions available in lesson
            if ($totalQuestions <= $lessonPointer->question_count + 1)
                $this->setEndPoint($lessonPointer);
        }

    }

    /**
     * Stop the lesson, so that tutor can start another lesson
     */
    public function stopLesson(){
        //Get the first choice of the module the student/tutor pick
        $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', Auth::user()->id)->first();
        //Get the lesson pointer
        $lessonPointer = LessonPointer::where('module_id', '=', $firstChoiceModule->module_id)->first();

        if($lessonPointer != null){
            $lessonPointer->delete();
        }
    }

    /**
     * Set true on the end point of the lesson pointer
     * @param $lessonPointer
     */
    private function setEndPoint($lessonPointer)
    {
        //Set the end point to be true.
        $lessonPointer->timestamps = false;
        $lessonPointer->end_point = true;
        $lessonPointer->save();
    }

    /**
     * Adding question count by 1 in the lesson pointer
     * @param $lessonPointer
     */
    private function addingQuestionCount($lessonPointer)
    {
        //Check the question count compared to the size of the lesson total questions
        $lessonPointer->question_count = $lessonPointer->question_count + 1;
        $lessonPointer->timestamps = false;
        $lessonPointer->save();
    }

    /**
     * Create a polling for tutor to create a question
     * @return polling main page
     */
    public function createPoll()
    {
        //Get all the input
        $post = Input::all();

        //Add to the database
        $pollQuestion = new question();

        //Store the error list
        $error = array();

        //Check error
        $error = $this->checkError($post, $error);

        //If there is no error
        if (empty($error)) {

            //Add the user ID and the correct optional answer ID
            $pollQuestion->user_id = Auth::user()->id;
            $pollQuestion->correct_id = $post['correctAnswerOption'];
            //Save the lesson
            $pollQuestion->lesson_id = $post['lessonList'];
            //Save the main question
            $pollQuestion->question = $post['mainQuestion'];
            $pollQuestion->timestamps = true;
            //Save the question
            $pollQuestion->save();

            //Save all the optional answers
            for ($i = 1; $i <= sizeof($post) - 5; $i++) {
                if ($i == $post['correctAnswerOption']) {
                    //Create optional answer
                    $optionalAnswers = new optionalAnswers();
                    $optionalAnswers->question_id = $pollQuestion->id;
                    $optionalAnswers->optional_answer = $post['optionalAnswers' . $i];
                    $optionalAnswers->save();
                    //Save the correct id
                    $pollQuestion->correct_id = $optionalAnswers->id;
                } else {
                    //Create optional answer
                    $optionalAnswers = new optionalAnswers();
                    $optionalAnswers->question_id = $pollQuestion->id;
                    $optionalAnswers->optional_answer = $post['optionalAnswers' . $i];
                    $optionalAnswers->save();
                }
            }

            //If the correct answer option value is 0
            //then we will make it as N/A
            //Save the question model again
            if ($post['correctAnswerOption'] == 0) {
                $pollQuestion->correct_id = 0;
                $pollQuestion->save();
            } else {
                $pollQuestion->save();
            }

            //Create a session that tell the tutor they have successfully created a polling.
            session(['pollingSuccess' => 'Polling has been created successfully']);
            //Create a session for module and lesson
            session(['lessonID' => $post['lessonList']]);
            session(['moduleID' => $post['moduleList']]);

        } else {
            session(['pollingError' => $error]);
        }

        //Redirect back to the polling page
        return redirect('/polling');

    }

    /**
     * Check if there are any error on the submitting polling form.
     * @param $post
     * @param $error
     * @param $question
     * @return if there is an error
     */
    private function checkError($post, $error)
    {
        //Check if the lesson is null
        if (!Input::has('lessonList')) {
            array_push($error, 'Lesson cannot be empty');

            //Check if this lesson belong to this module
        } else {
            //Get the module ID and lesson ID
            $moduleID = $post['moduleList'];
            $lessonID = $post['lessonList'];
            //Get the lesson model
            $lesson = Lesson::where('id', '=', $lessonID)->first();
            //Check if the lesson->module_ID is same as module_ID
            if ($lesson->module_id != $moduleID) {
                array_push($error, 'This lesson do not belong to this module');
            }
        }

        //Check if there is a question for the polling, if not , return a error.
        if ($post['mainQuestion'] == null) {
            array_push($error, 'Please fill in the main question');
        }

        //Check if all the optional answer is not empty
        for ($i = 1; $i <= sizeof($post) - 5; $i++) {
            $optionalAnswers = $post['optionalAnswers' . $i];
            if ($optionalAnswers == null) {
                array_push($error, 'optional Answer ' . $i . ' has no answer');
            }
        }
        return $error;

    }

    /**
     * Save the student response
     */
    public function saveResponse()
    {
        //Get the value
        $optionalAnswerValue = request()->optionalAnswerValue;
        //Get the question value
        $questionValue = request()->questionValue;

        $responseExist = $this->checkQuestionExistInUserResponse($questionValue);
        //Optional answer value belong to the question
        $optionalExist = $this->checkOptionalExistInQuestion($questionValue, $optionalAnswerValue);

        if ($optionalExist) {
            if (!$responseExist) {
                //Create a new response
                //And put all the data inside here
                // Save
                $response = new Response();
                $response->optionalAnswer_id = $optionalAnswerValue;
                $response->question_id = $questionValue;
                $response->user_id = Auth::user()->id;
                $response->save();
                return $questionValue;
            } else {
                return 'responseExist';
            }
        } else {
            return 'optionalNotExist';
        }
    }

    /**
     * Get the total amount of the lesson on the module that is being selected.
     */
    public function getTotalAmountLesson()
    {
        return Lesson::where('module_id', '=', request()->moduleID)->count();
    }

    /**
     * This is used for the one for create new question
     * @return a list of lesson from this module
     */
    public function getLessonsFromModule()
    {
        $lessons = Lesson::where('module_id', '=', request()->moduleID)->get();

        //Get the first lesson all the questions
        //if there are no lessons, then we should ignored
        if (sizeof($lessons) > 0) {
            $questions = $lessons[0]->questions;
        } else {
            $questions = null;
        }

        $data = array(
            'lessons' => $lessons,
            'questions' => $questions,
        );

        return $data;
    }

    /**
     * @return data if new classroom polling exist
     * Use for student to check if there is new polling to fill.
     */
    public function getClassroomPolling()
    {
        //Get the current user
        $user = User::find(Auth::user()->id);
        //Find their first choice module
        $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user->id)->first();

        //If user is a student, then we start to check whether to reload the page
        if ($user->hasRole('student')) {

            //Check if this user has his main module
            if ($firstChoiceModule != null) {
                $questionCount = question::where('module_id', '=', $firstChoiceModule->module_id)->count();

                //check polling count session and $question count
                $data = $this->checkPollingCountSession($questionCount);

            } else {
                $data = 'No Data';
            }
        } else {
            $data = 'No Data';
        }

        return $data;
    }

    /**
     * For create new lesson
     * @return A list of the lessons
     */
    public function getAllLessonsFromModule()
    {
        $lessons = Lesson::where('module_id', '=', request()->moduleID)->get();
        $moduleName = Module::find(request()->moduleID)->module_name;

        $data = array(
            'lessons' => $lessons,
            'moduleName' => $moduleName,
        );
        return $data;
    }

    /**
     * return a list of questions from this lesson
     * @return A list of questions from this lesson
     */
    public function getQuestionsFromLesson()
    {
        //Get the lesson ID
        $lessonID = request()->lessonID;
        $lessonName = Lesson::find($lessonID)->lesson_name;
        $questions = Lesson::find($lessonID)->questions;

        $data = array(
            'lessonName' => $lessonName,
            'questions' => $questions,
        );

        return $data;
    }


    /**
     * Check if the polling count is same as the session
     * If not, then we should reload the page for new classroom polling content
     * @param $questionCount
     * @return $data
     */
    private function checkPollingCountSession($questionCount)
    {
        //Check if session exist, if not, then create a new one
        //The session used to store amount of classroom polling in
        if (session()->has('pollingCount')) {
            //Get the polling count total
            $pollingCount = session()->get('pollingCount');

            //Check if polling count from session is same as $question->count()
            if ($pollingCount != $questionCount) {
                session(['pollingCount' => $questionCount]);
                $data = 'data';
            } else {
                $data = 'No Data';
            }

        } else {
            session(['pollingCount' => $questionCount]);
            $data = 'No Data';
        }

        return $data;
    }

    /**
     * Check if the response exist
     * @param $questionValue
     * @return bool
     */
    private function checkQuestionExistInUserResponse($questionValue)
    {
        //Check if this question already exist in the user response
        $response = Response::where('user_id', '=', Auth::user()->id)
            ->where('question_id', '=', $questionValue)->exists();

        if ($response) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $questionValue
     * @param $optionalAnswerValue
     * Check if the optional answer is inside the question
     * @return boolean whether exist or not, exist = true, not = false
     */
    private function checkOptionalExistInQuestion($questionValue, $optionalAnswerValue)
    {

        // use optional answer model to find the question ID
        $question = question::find($questionValue);
        $optionalExist = false;

        //Check if this optional answer is inside question , if not that mean
        //user have change the ID in the console code
        if ($question != null) {
            foreach ($question->optionalAnswers as $optional) {
                if ($optional->id == $optionalAnswerValue) {
                    $optionalExist = true;
                }
            }
        }

        return $optionalExist;

    }


}
