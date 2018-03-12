<?php

namespace attendance\Http\Controllers;

use attendance\optionalAnswers;
use attendance\question;
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

}
