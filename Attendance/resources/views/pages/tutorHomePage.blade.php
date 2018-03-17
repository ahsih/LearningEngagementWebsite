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
                <p class="text-center"> Module already existed!</p>
            </div>
        </div>
    </div>
@stop
@section('polling feature')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h2 class="module-bottom-zero font-navy">@if ($moduleName != null)Classroom Polling Result For Module:
                {{ $moduleName }}
                @else You have No Classroom Polling Module
                @endif
                <button class="btn btn-warning pull-right" id="directToPolling">Create classroom polling</button>
            </h2>
            <div class="panel-body">
                @if($questions != null)
                    @foreach($questions as $question)
                        <div id="tutorQuestionPolling" onmouseover="createChart('{{ $question->question}}');">
                            <canvas id="pollingChart"></canvas>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@stop