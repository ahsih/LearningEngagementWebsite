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
                <h4>Select the current module to your list</h4>
                <input type="button" class="btn btn-success" id="expandModules" value="Module List"></br>
                <hint>You can ask your module tutor to remove you from the module</hint>
            </div>
        </div>
    </div>
@stop
@section('polling feature')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h2 class="module-bottom-zero font-navy">Classroom polling</h2>
            <div class="panel-body">
                @foreach($questions as $question)
                    <div>
                        <p>{{ $question->question }}</p>
                        @foreach($question->optionalAnswers as $optionalAnswer)
                            <p>{{ $optionalAnswer->optional_answer }}</p>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop