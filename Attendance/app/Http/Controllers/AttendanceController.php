<?php

namespace attendance\Http\Controllers;

use attendance\FirstChoiceUserModule;
use attendance\LessonStart;
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
     * Record the attendance for this user
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
                if (!$lessonTime > Carbon::now()) {
                    $this->createLessonStart($firstChoiceModule->module_id);
                    $result = 'true';
                }else{
                    $result = 'false';
                }
            }
        }

        return $result;

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
