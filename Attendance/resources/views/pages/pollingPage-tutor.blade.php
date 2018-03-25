@extends('layouts.otherPage') @section('pageContent')
    @if($role == 'tutor')
        <div class="container lightBlue">
            <div class="panel panel-heading">
                <h2 class="module-bottom-zero margin-zero-top font-navy text-center"><b> Create Classroom polling</b>
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
                                <h4 class="module-bottom-zero margin-zero-top font-navy">Create new lesson</h4>
                                <small class="text-info">Insert your lesson name below here</small>
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
                                    <label>Lesson Name:</label>
                                    <input type="text" name="lessonName" class="form-control"
                                           placeholder="Lesson Name"/>
                                </div>
                                <input type="hidden" name="hiddenAmountOfLesson" id="hiddenAmountOfLesson"
                                       value="{{ $totalAmountLesson }}"/>
                                <p class="noMarginBottom text-success pull-left">Total Lesson:</p>
                                <p class="text-primary"><b id="amountOfLesson">{{ $totalAmountLesson }}</b></p>
                                <button type="submit" class="btn btn-success">Create new Lesson</button>
                                <hr>
                                {!! Form::close() !!}
                            </div>
                            <div id="createQuestionnaire">
                                {!! Form::open(['action' => 'PollingController@createPoll']) !!}
                                {!! Form::token() !!}
                                <h4 class="module-bottom-zero margin-zero-top font-navy">Create new question </h4>
                                <div class="form-group">
                                    <label>Module:</label>
                                    <select class="form-control" name="moduleList" id="moduleListPolling">
                                        @foreach($modules as $module)
                                            <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Lesson:</label>
                                    <select class="form-control" name="lessonList" id="lessonList">
                                        @foreach($lessons as $lesson)
                                            <option value="{{ $lesson->id }}">{{ $lesson->lesson_name }}</option>
                                        @endforeach
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
                                <button type="submit" class="pull-right btn btn-success">Create new classroom polling
                                </button>
                                <br>
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div id="QuestionInThisModule">
                                <br>
                                <h4 class="module-bottom-zero margin-zero-top font-navy text-center">List of lesson in
                                    this
                                    module: {{ $modules[0]->module_name }}</h4>
                                <hr>
                                <div id="listOfLessons">
                                    @foreach($lessons as $lesson)
                                        <h5 class="margin-zero-top noMarginBottom font-navy">{{ $lesson->lesson_name }}</h5>
                                    @endforeach
                                </div>
                            </div>
                            <hr>
                            <div id="Questions in this Lesson">
                                <h4 class="module-bottom-zero margin-zero-top font-navy text-center">List of question in
                                    this
                                    lesson: {{ $lessons[0]->lesson_name }}</h4>
                                <hr>
                                <div id="listOfQuestions">
                                    @foreach($lessons[0]->questions as $question)
                                        <h5 class="margin-zero-top noMarginBottom font-navy">{{ $question->question_name }}</h5>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop