@extends('layouts.otherPage') @section('pageContent')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h2 class="module-bottom-zero margin-zero-top font-navy"><b> Create Classroom polling</b></h2>
            <div class="panel-body">
                <div class="text-danger">
                    @if(Session::has('pollingError'))
                        @foreach(Session::get('pollingError') as $error)
                            <p class="noMarginBottom">{{ $error }}</p>
                        @endforeach
                    @endif
                    {{ Session::forget('pollingError') }}
                </div>
                <div class="text-success">
                    @if(Session::has('pollingSuccess'))
                        <p class="noMarginBottom">{{ Session::get('pollingSuccess') }}</p>
                        @endif
                    {{ Session::forget('pollingSuccess') }}
                </div>
                {!! Form::open(['action' => 'PollingController@createPoll']) !!}
                <div class="form-group">
                    <label>Module:</label>
                    <select class="form-control" name="moduleList">
                        @foreach($modules as $module)
                            <option value="{{ $module->id }}">{{ $module->module_name }}</option>
                        @endforeach
                    </select>
                </div>
                <label for="question">Main Question:</label>
                <input type="text" class="form-control" id="question" name="mainQuestion" placeholder="Question"/>
                <div id="optionalAnswerBox">
                    <input type="hidden" id="optionalAnswersCount" value="2"/>
                    <label>Optional Answer</label>
                    <input type="text" class="form-control" name="optionalAnswers1" placeholder="optional answer 1"/>
                    <label>Optional Answer 2</label>
                    <input type="text" class="form-control" name="optionalAnswers2" placeholder="optional answer 2"/>
                </div>
                <div class="pull-right">
                    <a id="addMoreAnswer" href="#"><p class="glyphicon glyphicon-plus"></p>Add more answers</a>
                </div>
                <br>
                <div class="form-group">
                    <label>Pick the correct optional answer (if there isn't any,then put as 'No correct answer')</label>
                    <select class="form-control" id="correctAnswerOption" name="correctAnswerOption">
                        <option value="0">No correct answer</option>
                        <option value="1">Optional Answer 1</option>
                        <option value="2">Optional Answer 2</option>
                    </select>
                </div>
                <br><br>
                <button type="submit" class="pull-right btn btn-success">Create new classroom polling</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop