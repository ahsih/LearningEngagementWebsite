@extends('layouts.homePage') @section('module')
<div class="container">
	<div class="panel panel-default">
		<div class="panel-body">
			<Label id="modules">Your Modules:
			@foreach ($modules as $module)
			{{$module->name}} + " "
			@endforeach
			</label>
			</br>
			<h4>Add New Module:</h4>
			<label> Module Name:</label> 
			<input type="text" name="module_name" id="moduleName" placeHolder="put your module name here" class="moduleBoxLarge"/>
			<input type="button" class="btn btn-success" id="addModule" value="Add Module"></button></br>
			<hint>Your module will be your group live-chat name! If you do wish to delete your module, please contact administrator.</hint>
		</div>
	</div>
</div>
@stop