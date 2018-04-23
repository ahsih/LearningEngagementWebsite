@extends('layouts.homePage') @section('module')
    <div class="container lightBlue">
        <div class="panel panel-default">
            <div class="panel-body">
                <!-- Get all the modules the tutor teaches -->
                <Label id="modules">Your Modules:
                    <span class="text-danger">
                    @foreach ($modules as $module)
                            '{{ $module->module_name }}'
                        @endforeach
                    </span>
                </label>
                </br>
                <h4>Add A New Module:</h4>
                <label> Module Name:</label>
                <input type="text" name="module_name" id="moduleName" placeHolder="put your module name here"
                       class="moduleBoxLarge"/>
                <input type="button" class="btn btn-success" id="addModule" value="Add Module"></br>
                <h4>Select One Of The Existing Modules</h4>
                <input type="button" class="btn btn-success" id="expandModules" value="Module List"></br>
            </div>
        </div>
    </div>
    <div id="moduleSuccess" class="addModule-error">
        <div id="popup-wrapper">
            <div class="alert alert-success">
                <a class="pull-right glyphicon glyphicon-remove" id="closeModuleSuccess"></a>
                <p class="text-center"> Module has been added</p>
            </div>
        </div>
    </div>
    <div id="moduleError" class="addModule-error">
        <div id="popup-wrapper">
            <div class="alert alert-warning">
                <a class="pull-right glyphicon glyphicon-remove" id="closeModuleAlert"></a>
                <p class="text-center"> Module is already existed or is empty!</p>
            </div>
        </div>
    </div>
@stop
@section('polling feature')
    <!-- Tutor polling -->
    <div class="panel panel-heading">
        <h2 class="module-bottom-zero font-navy">@if ($moduleName != null)Polling Result For Module:
            {{ $moduleName }}
            @else You Do Not Have Classroom Polling Module
            @endif
        </h2>
        <button class="btn btn-primary center-block" id="directToPolling">Create Classroom Polling</button>
        <div id="selectLessonList">
            @if($activeLesson != null || $activeLesson != 0)
                @if(!$activeLesson->end_point)
                    <button type="submit" class="btn btn-success btn-lg" id="nextQuestion">Next</button>
                @else
                    <small>If you click on the stop button now, other students may not receive your questions!</small>
                @endif
                <button type="submit" class="btn btn-danger btn-lg" id="stopLesson">Stop</button>
            @else
                @if($lessons != null && sizeof($lessons) > 0)
                    <h4 class="noMarginBottom margin-zero-top font-navy">Pick A Polling</h4>
                    {!! Form::open(['action' => 'PollingController@createActiveLesson']) !!}
                    {!! Form::token() !!}
                    <select id="firstModuleLessonList" name="firstModuleLessList">
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}">{{ $lesson->lesson_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Start the polling</button>
                    {!! Form::close() !!}
                @else
                    <h5> No lessons Has Been Created In This Module At The Moment!</h5>
                @endif
            @endif
        </div>
        <div class="panel-body" id="pollingGraph">
            @if($activeLesson != null)
                @if($activeLesson->lesson->questions != null && sizeof($activeLesson->lesson->questions) > 0)
                    @for($i = $activeLesson->question_count;$i > -1; $i--)
                        <div class="tutorQuestionPolling">
                            <input type="hidden" value="{{ $activeLesson->lesson->questions[$i]->id }}"/>
                            <canvas id="pollingChart{{$activeLesson->lesson->questions[$i]->id}}"></canvas>
                        </div>
                    @endfor
                @else
                    <p> You Do Not Have Any Questions In This Module At The Moment!</p>
                @endif
            @endif
        </div>
    </div>
@stop