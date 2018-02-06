@extends('layouts.homePage') @section('module')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-body">
                <!-- Get all the modules the tutor teaches -->
                <Label id="modules">Your Modules:
                    @foreach ($modules as $module)
                        {{ $module->module_name }}
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
    <div id="moduleError" class="addModule-error">
        <div id="popup-wrapper">
            <div class="alert alert-warning">
                <a class="pull-right glyphicon glyphicon-remove" id="closeModuleAlert"></a>
                <p class="text-center"> Module already existed!</p>
            </div>
        </div>
    </div>
@stop