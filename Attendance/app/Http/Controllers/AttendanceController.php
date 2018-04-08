<?php

namespace attendance\Http\Controllers;

use attendance\FirstChoiceUserModule;
use attendance\LessonStart;
use attendance\LoginTime;
use attendance\StudentAttendance;
use attendance\User;
use Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
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
     * Set the attendance of the student
     * @param $userID
     */
    public function setAttendance($userID)
    {
        //Get the first choice module from this user ID
        $firstChoice = FirstChoiceUserModule::where('user_id', '=', $userID)->first();

        //If first choice is not null
        //Then check if the module has lesson started
        if ($firstChoice != null) {
            //Then get a list of lesson starts from this module
            $lessonStart = LessonStart::latest('start_time')
                ->where('module_id', '=', $firstChoice->module_id)->first();
            if ($lessonStart != null) {
                //Check if this student has already register in this lesson
                $studentAttendance = StudentAttendance::latest('lessonStart_id')->where('user_id', '=', $userID)->first();
                if ($studentAttendance == null) {
                    $withinTime = $this->checkUserWithinValidTime($userID,$lessonStart->start_time);
                    if($withinTime){
                        $this->recordUserIntoAttendance($userID,$firstChoice->module_id,$lessonStart->id);
                    }
                }
            }
        }
    }

    /**
     * Record this user into the attendance
     * @param $userID
     * @param $moduleID
     * @param $lessonStartID
     */
    private function recordUserIntoAttendance($userID,$moduleID,$lessonStartID){
        //Save student attendance
        $studentAttendance = new StudentAttendance();
        $studentAttendance->user_id = $userID;
        $studentAttendance->module_id = $moduleID;
        $studentAttendance->lessonStart_id = $lessonStartID;
        $studentAttendance->save();
    }

    /**
     * Check if the user is within the valid time
     * @param $userID
     * @return bool
     */
    private function checkUserWithinValidTime($userID, $startTime)
    {
        //Get the user login time
        $userLoginTime = LoginTime::where('user_id', '=', $userID)->first();
        //Convert user login time to carbon
        $userLoginTime = Carbon::parse($userLoginTime->login_time);
        //Convert lessonStart time to carbon
        $lessonStartTime = Carbon::parse($startTime);
        if ($userLoginTime > $lessonStartTime->subMinute(20) && $userLoginTime < $lessonStartTime->addHour(1)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Record the attendance for this module - by tutor
     */
    public function recordAttendance()
    {
        $user = User::find(Auth::user()->id);
        $firstChoiceModule = FirstChoiceUserModule::where('user_id', '=', $user->id)->first();

        $result = 'false';

        if ($firstChoiceModule != null) {
            $lessonStart = LessonStart::orderBy('start_time')
                ->where('module_id', '=', $firstChoiceModule->module_id)->first();
            if ($lessonStart == null) {
                $this->createLessonStart($firstChoiceModule->module_id);
                $result = 'true';
            } else {
                //Get the current time stamp
                $lessonTime = $lessonStart->start_time;
                $lessonTime = Carbon::parse($lessonTime);
                //Add extra an hour
                $lessonTime->addHour(1);
                //If the lesson time is bigger than the now, then we should not create a new lesson
                //Because it's not finish yet.
                if ($lessonTime < Carbon::now()) {
                    $this->createLessonStart($firstChoiceModule->module_id);
                    $result = 'true';
                } else {
                    $result = 'false';
                }
            }
        }

        return $result;

    }

    /**
     * Set the user to the login if it's necessary
     * @param $userID
     */
    public function setUserToLogin($userID)
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
     * Create a new lesson start
     * @param $moduleID - the module ID
     */
    private function createLessonStart($moduleID)
    {
        //Create a new lesson start
        $lessonStart = new LessonStart();
        $lessonStart->module_id = $moduleID;
        $lessonStart->start_time = Carbon::now();
        $lessonStart->timestamps = false;
        $lessonStart->save();
    }

}