@extends('layouts.otherPage') @section('pageContent')
    @if($role == 'tutor')
        <div class="container lightBlue">
            <div class="panel panel-heading">
                <h2 class="module-bottom-zero margin-zero-top font-navy text-center"><b> Create Classroom Polling</b>
                </h2>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <div class="text-danger">
                                @if(Session::has('pollingError'))
                                    @foreach(Session::get('pollingError') as $error)
                                        <p class="noMarginBottom"><b>{{ $error }}</b></p>
                                    @endforeach
                                @endif
                                {{ Session::forget('pollingError') }}
                            </div>
                            <div class="text-success">
                                @if(Session::has('pollingSuccess'))
                                    <p class="noMarginBottom"><b>{{ Session::get('pollingSuccess') }}</b></p>
                                @endif
                                {{ Session::forget('pollingSuccess') }}
                            </div>
                            <div id="createLesson">
                                <h4 class="module-bottom-zero margin-zero-top font-navy">Create A New Polling</h4>
                                <small class="text-info">Insert your polling name below here</small>
                                {!! Form::open(['action' => 'PollingController@createLesson']) !!}
                                {!! Form::token() !!}
                                <hr>
                                <div class="form-group">
                                    <label>Module:</label>
                                    <select class="form-control" name="moduleListLesson" id="moduleListLesson">
                                        @foreach($modules as $module)
                                            <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                        @endforeach
                                    </select>
                                    <br>
                                    <label>Polling Name:</label>
                                    <input type="text" name="lessonName" class="form-control"
                                           placeholder="Lesson Name"/>
                                </div>
                                <input type="hidden" name="hiddenAmountOfLesson" id="hiddenAmountOfLesson"
                                       value="{{ $totalAmountLesson }}"/>
                                <p class="noMarginBottom text-success pull-left">Total Polling:</p>
                                <p class="text-primary"><b id="amountOfLesson">{{ $totalAmountLesson }}</b></p>
                                <button type="submit" class="btn btn-success">Create New Polling</button>
                                <hr>
                                {!! Form::close() !!}
                            </div>
                            <div id="createQuestionnaire">
                                {!! Form::open(['action' => 'PollingController@createPoll']) !!}
                                {!! Form::token() !!}
                                <h4 class="module-bottom-zero margin-zero-top font-navy">Create New Question </h4>
                                <small>Student will receive one reward point in this module every time they answer the correct answer in the question</small>
                                <div class="form-group">
                                    <label>Module:</label>
                                    <select class="form-control" name="moduleList" id="moduleListPolling">
                                        @foreach($modules as $module)
                                            @if(Session::has('moduleID'))
                                                @if(Session::get('moduleID') == $module->id)
                                                    <option value="{{ $module->id }}"
                                                            selected>{{ $module->module_name }}</option>
                                                @else
                                                    <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                                @endif
                                            @else
                                                <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                            @endif

                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Polling:</label>
                                    <select class="form-control" name="lessonList" id="lessonList">
                                        @if($lessons != null)
                                            @foreach($lessons as $lesson)
                                                @if(Session::has('lessonID'))
                                                    @if(Session::get('lessonID') == $lesson->id)
                                                        <option value="{{ $lesson->id }}"
                                                                selected>{{ $lesson->lesson_name }}</option>
                                                    @else
                                                        <option value="{{ $lesson->id }}">{{ $lesson->lesson_name }}</option>
                                                    @endif
                                                @else
                                                    <option value="{{ $lesson->id }}">{{ $lesson->lesson_name }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <label for="question">Main Question:</label>
                                <input type="text" class="form-control" id="question" name="mainQuestion"
                                       placeholder="Question"/>
                                <div id="optionalAnswerBox">
                                    <input type="hidden" id="optionalAnswersCount" value="2"/>
                                    <label>Optional Answer</label>
                                    <input type="text" class="form-control" name="optionalAnswers1"
                                           placeholder="optional answer 1"/>
                                    <label>Optional Answer 2</label>
                                    <input type="text" class="form-control" name="optionalAnswers2"
                                           placeholder="optional answer 2"/>
                                </div>
                                <div class="pull-right">
                                    <a id="addMoreAnswer" href="#"><p class="glyphicon glyphicon-plus"></p>Add more
                                        answers</a>
                                </div>
                                <br>
                                <div class="form-group">
                                    <label>Pick the correct optional answer (if there isn't any,then put as 'No correct
                                        answer')</label>
                                    <select class="form-control" id="correctAnswerOption" name="correctAnswerOption">
                                        <option value="0">No correct answer</option>
                                        <option value="1">Optional Answer 1</option>
                                        <option value="2">Optional Answer 2</option>
                                    </select>
                                </div>
                                <button type="submit" class="pull-right btn btn-success">Create New Classroom Polling
                                </button>
                                <br>
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div id="LessonInThisModule">
                                <br>
                                <h4 id="listOfLessonTitle"
                                    class="module-bottom-zero margin-zero-top font-navy text-center">@if(sizeof($modules) > 0)
                                        List of the pollings in
                                        this
                                        module: {{ $modules[0]->module_name }}
                                    @else You do not have modules!
                                    @endif</h4>
                                <hr>
                                <div id="listOfLessons">
                                    @if($lessons != null)
                                        @foreach($lessons as $lesson)
                                            <h5 class="margin-zero-top noMarginBottom font-navy">{{ $lesson->lesson_name }}</h5>
                                        @endforeach
                                    @endif
                                </div>
                                <hr>
                            </div>
                            <div id="Questions in this Lesson">
                                <h4 id="questionTitle" class="module-bottom-zero margin-zero-top font-navy text-center">
                                    @if($lessons != null && sizeof($lessons) > 0)
                                        List of the questions in
                                        this
                                        polling: {{ $lessons[0]->lesson_name }}
                                    @else
                                        You do not have a polling yet!
                                    @endif</h4>
                                <small>This change once you changes the 'Polling' drop-down box in 'Create New Question'
                                </small>
                                <hr>
                                <div id="listOfQuestions">
                                    @if(Session::has('lessonID'))
                                        @foreach(\attendance\Lesson::find(Session::get('lessonID'))->questions as $question)
                                            <h5 class="margin-zero-top noMarginBottom font-navy">{{ $question->question }}</h5>
                                        @endforeach
                                    @else
                                        @if($lessons != null && sizeof($lessons) > 0)
                                            @foreach($lessons[0]->questions as $question)
                                                <h5 class="margin-zero-top noMarginBottom font-navy">{{ $question->question }}</h5>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop