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
                @if($questions != null)
                    @foreach($questions as $question)
                        <div id="question{{ $question->id }}">
                            <div class="panel-heading classroomHeading">
                                <p class="noMarginBottom">{{ $question->question }}</p>
                            </div>
                            @foreach($question->optionalAnswers as $optionalAnswer)
                                <a href="#" class="optionSelected">
                                    <input type="hidden" value="{{ $optionalAnswer->id }}"/>
                                    <input type="hidden" class="questionID" value="{{ $question->id }}"/>
                                    <div class="classroomAnswer">
                                        <p class="marginBottomByFive">{{ $optionalAnswer->optional_answer }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
@stop