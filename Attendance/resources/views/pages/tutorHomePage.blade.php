@extends('layouts.homePage') @section('module')
    <div class="container lightBlue">
        <div class="panel panel-default">
            <div class="panel-body">
                <!-- Get all the modules the tutor teaches -->
                <Label id="modules">Your Modules:
                    @foreach ($modules as $module)
                        '{{ $module->module_name }}'
                    @endforeach
                </label>
                </br>
                <h4>Add New Module:</h4>
                <label> Module Name:</label>
                <input type="text" name="module_name" id="moduleName" placeHolder="put your module name here"
                       class="moduleBoxLarge"/>
                <input type="button" class="btn btn-success" id="addModule" value="Add Module"></br>
                <h4>OR: Select the current module to your list</h4>
                <input type="button" class="btn btn-success" id="expandModules" value="Module List"></br>
                <hint>If you do wish to remove the module from your list, please contact administrator.
                </hint>
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
                <p class="text-center"> Module already existed or is empty!</p>
            </div>
        </div>
    </div>
@stop
@section('polling feature')
    <!-- Tutor polling -->
    <div class="panel panel-heading">
        <h2 class="module-bottom-zero font-navy">@if ($moduleName != null)Polling Result For Module:
            {{ $moduleName }}
            @else You have No Classroom Polling Module
            @endif
        </h2>
        <button class="btn btn-primary center-block" id="directToPolling">Create classroom polling</button>
        <div id="selectLessonList">
            @if($lessonPointer != null || $lessonPointer != 0)
                @if(!$lessonPointer->end_point)
                    <button type="submit" class="btn btn-success btn-lg" id="nextQuestion">Next</button>
                @else
                    <small>If you click on the stop button now, other students may not receive your questions!</small>
                @endif
                <button type="submit" class="btn btn-danger btn-lg" id="stopLesson">Stop</button>
            @else
                @if(sizeof($lessons) > 0)
                    <h4 class="noMarginBottom margin-zero-top font-navy">Pick A Lesson</h4>
                    {!! Form::open(['action' => 'PollingController@createLessonPointer']) !!}
                    {!! Form::token() !!}
                    <select id="firstModuleLessonList" name="firstModuleLessList">
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}">{{ $lesson->lesson_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Start the lesson</button>
                    {!! Form::close() !!}
                @else
                    <h5> No lessons in this module at the moment!</h5>
                @endif
            @endif
        </div>
        <div class="panel-body" id="pollingGraph">
            @if($lessonPointer != null)
                @if($lessonPointer->lesson->questions != null)
                    @for($i = $lessonPointer->question_count;$i > -1; $i--)
                        <?php
                        $question = $lessonPointer->lesson->questions[$i];
                        //get list of option
                        $optional = $question->optionalAnswers;
                        //Create an array to store the amount
                        $amountArray = array();
                        //Create an array to store the optional answer
                        $answerArray = array();
                        foreach ($optional as $option) {
                            $answer = $option->optional_answer;
                            $amount = \attendance\Response::where('optionalAnswer_id', '=', $option->id)->count();
                            array_push($amountArray, $amount);
                            array_push($answerArray, $answer);
                        }
                        //encode the amount
                        $amountArray = json_encode($amountArray);
                        //encode the answer
                        $answerArray = json_encode($answerArray);

                        ?>
                        <div id="tutorQuestionPolling" class="center-block"
                             onmouseover="createChart({{$question}},{{ $answerArray }},{{ $amountArray }});">
                            <canvas id="pollingChart{{$question->id}}"></canvas>
                        </div>
                    @endfor
                @endif
            @endif
        </div>
    </div>
@stop