<?php

namespace attendance\Http\Controllers;

use attendance\FirstChoiceUserModule;
use attendance\optionalAnswers;
use attendance\question;
use attendance\Response;
use attendance\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Input;

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

        //Get the users module
        $modules = User::find($request->user()->id)->modules;

        //Store the detail that will pass to the view
        $data = array(
            'modules' => $modules,
            'path' => $path,
            'title' => 'Classroom polling'
        );
        return view('pages.pollingPage')->with($data);
    }

    /**
     * @param Request $request
     * Create a polling for tutor to create a question
     * @return polling main page
     */
    public function createPoll()
    {
        //Get all the input
        $post = Input::all();

        //Add to the database
        $pollQuestion = new question();
        //Add the module id
        $pollQuestion->module_id = $post['moduleList'];
        $pollQuestion->user_id = Auth::user()->id;
        $pollQuestion->correct_id = $post['correctAnswerOption'];

        //Store the error list
        $error = array();

        //Check error
        $error = $this->checkError($post, $error);

        //If there is no error
        if (empty($error)) {
            //Save the main question
            $pollQuestion->question = $post['mainQuestion'];
            //Save the question
            $pollQuestion->save();

            //Save all the optional answers
            for ($i = 1; $i <= sizeof($post) - 4; $i++) {
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
            if ($post['correctAnswerOption'] == 0) {
                $pollQuestion->correct_id = 0;
                $pollQuestion->save();
            } else {
                $pollQuestion->save();
            }

            //Create a session that tell the tutor they have successfully created a polling.
            session(['pollingSuccess' => 'Polling has been created successfully']);

        } else {
            session(['pollingError' => $error]);
        }


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
        //Check if there is a question for the polling, if not , return a error.
        if ($post['mainQuestion'] == null) {
            array_push($error, 'Please fill in the main question');
        } else {
        }

        //Check if all the optional answer is not empty
        for ($i = 1; $i <= sizeof($post) - 4; $i++) {
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
     * @return data if new classroom polling exist
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
     * Check if the polling count is same as the session
     * If not, then we should reload the page for new classroom polling content
     * @param $questionCount
     * @return $data
     */
    private function checkPollingCountSession($questionCount){
        //Check if session exist, if not, then create a new one
        //The session used to store amount of classroom polling in
        if (session()->has('pollingCount')) {
            //Get the polling count total
            $pollingCount = session()->get('pollingCount');

            //Check if polling count from session is same as $question->count()
            if ($pollingCount != $questionCount) {
                session(['pollingCount' => $questionCount]);
                $data = 'data';
            }else{
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
