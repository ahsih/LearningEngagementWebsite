@extends('layouts.homePage') @section('module')
    <div class="container lightBlue">
        <div class="panel panel-default">
            <div class="panel-body">
                <!-- Get all the modules the tutor teaches -->
                <Label id="modules">Your Study Modules:
                    @foreach ($modules as $module)
                        {{ $module->module_name }}
                    @endforeach
                </label>
                </br>
                <h4>Request to join the module</h4>
                <input type="button" class="btn btn-success" id="expandModules" value="Module List"></br>
                <hint>You can ask your module tutor to remove you from the module</hint>
            </div>
        </div>
    </div>
@stop
@section('polling feature')
    <div class="panel panel-heading">
        <h2 class="module-bottom-zero font-navy">@if ($moduleName != null)Classroom Polling Module:
            {{ $moduleName }}
            @else You Do Not Have A Classroom Polling Module
            @endif </h2>
        <div class="panel-body">
            <div id="studentPollingNotifications"></div>
            @if($questions != null && sizeof($questions) > 0)
                @for($i = 0;$i <= $activeLesson->question_count;$i++)
                    @if(!\attendance\Response::where('question_id','=',$questions[$i]->id)
                ->where('user_id','=',Auth::user()->id)
                ->exists())
                        <div id="question{{ $questions[$i]->id }}">
                            <div class="panel-heading classroomHeading">
                                <p class="noMarginBottom">{{ $questions[$i]->question }}</p>
                            </div>
                            @foreach($questions[$i]->optionalAnswers as $optionalAnswer)
                                <a href="#" class="optionSelected">
                                    <input type="hidden" value="{{ $optionalAnswer->id }}"/>
                                    <input type="hidden" class="questionID" value="{{ $questions[$i]->id }}"/>
                                    <div class="classroomAnswer">
                                        <p class="marginBottomByFive">{{ $optionalAnswer->optional_answer }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-primary"> You have already filled the question: <span
                                    class="text-danger">{{ $questions[$i]->question }}</span></p>
                    @endif
                @endfor
            @else
                <p> No lesson polling has been started yet</p>
            @endif
        </div>
    </div>
@stop
@section('reward')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h2 class="noMarginBottom margin-zero-top font-navy text-center">Reward</h2>
            <div class="panel-body">
                    @if($rewardAchieve != null)
                    <h4 class="margin-zero-top">Your current reward point on this module: {{ $moduleName }} is: <b class="text-danger circleNumber">{{ $rewardAchieve->amount }}</b></h4>
                    @endif
                @if($rewardList != null && sizeof($rewardList) > 0)
                    @foreach($rewardList as $reward)
                        <p class="pull-left">Prize: {{ $reward->reward_name }}</p>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@stop