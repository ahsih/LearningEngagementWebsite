@extends('layouts.homePage') @section('module')
    <div class="container">
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