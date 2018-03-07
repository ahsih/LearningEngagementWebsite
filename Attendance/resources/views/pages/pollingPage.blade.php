@extends('layouts.otherPage') @section('pageContent')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h2 class="module-bottom-zero margin-zero-top font-navy"><b> Create Classroom polling</b></h2>
            <div class="panel-body">
                {!! Form::open(['action' => 'PollingController@createPoll']) !!}
                <label for="question">Main Question:</label>
                <input type="text" class="form-control" id="question"/>

            </div>
        </div>
    </div>
@stop