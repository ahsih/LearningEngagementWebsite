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
                <hint>Your module will be your group live-chat name! If you do wish to delete your module, please
                    contact administrator.
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
@section('live-chat')
    <div class="container">
        <div class="panel panel-heading">
            <h2 class="module-bottom-zero">@if (count($conversations) > 0)
                    Live-Chat-{{ $moduleName }}
                @else You have no live-chat module
                @endif
                <button class="pull-right btn btn-warning" id="changeLiveChat">Live-Chat Setting <span
                            class="glyphicon glyphicon-asterisk"></span></button>
            </h2>
            <!-- chat message -->
            <div class="panel panel-body">
                <!-- Live chat -->
                <div class="scrollable-chat">
                    @foreach($conversations as $conversation)
                        <ul class="list-inline setToZero">
                            <li><p class="pull-left text-danger">{{ Auth::user()->name }}:</p></li>
                            <li><p class="pull-left text-success">{{ $conversation->message }}</p></li>
                            <li><p class="pull-left text-info">{{ $conversation->created_at }}</p></li>
                        </ul>
                    @endforeach
                </div>
                <label class="label-font-big"> Send Text </label>
                <input type="text" id="sendTextChat" placeholder="Type the message you want to send here"
                       class="moduleBoxLarge"/>
                <input type="button" id="SendText" value="Send Message" class="btn btn-success"/>
                <label id="messageConfirmation"></label>
            </div>
        </div>
    </div>
    <!-- hidden div, which display the setting of the live chat -->
    <div id="liveChatSettings" class="liveChat-popup">
        <div id="popup-wrapper">
            <div class="alert alert-info">
                <a class="pull-right glyphicon glyphicon-remove" id="closeLiveChatSettings"></a>
                <h5> Change your live-chat module</h5>
                <select class="selectpicker" id="liveChatModuleID">
                    <optgroup label="Modules">
                    @foreach ($modules as $module)
                    <option value="{{ $module->id  }}"> {{ $module->module_name }}</option>
                    @endforeach
                    </optgroup>
                </select>
                <input type="button" id="changeModule" value="Submit" class="btn btn-success"/>
            </div>
        </div>
    </div>
@stop