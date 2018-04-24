<!-- hidden div, which display the setting of the live chat -->
<div id="liveChatSettings" class="hidden-popup">
    <div id="popup-wrapper">
        <div class="alert alert-info">
            <br>
            <h5 class="pull-left"> Change your live-chat module</h5>
            <a class="pull-right glyphicon glyphicon-remove" id="closeLiveChatSettings"></a>
            <select id="liveChatModuleID" class="form-control">
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
<!-- liveUser right box -->
@if($role == 'tutor')
    <div class="liveUser-box">
        <div id="liveUser-innerBox">
            <div id="liveUser">
                <h2 class="font-navy">Live Users</h2>
                <?php $userOnline = false;  $amountOfUsers = 0?>
                @if($listUsersInThisModule != null)
                    <ul id="listOfOnlineUsers">
                        @foreach($listUsersInThisModule as $user)
                            @if($user->loginTime != null)
                                @if(!$user->loginTime->logout)
                                    <li class="noMarginBottom text-primary">{{ $user->name }}</li>
                                    <?php $userOnline = true; $amountOfUsers++; ?>
                                @endif
                            @endif
                        @endforeach
                    </ul>
                @endif
                @if(!$userOnline)
                    <h4 class="font-navy" id="NoUsersOnline">None of the users is online at the moment</h4>
                @else
                    <h5 class="text-success" id="totalOnlineUsers">Total Online: {{ $amountOfUsers }} users</h5>
                @endif
            </div>
        </div>
    </div>
@endif
<!-- Hidden module selection -->
<div id="moduleList" class="moduleList-popup">
    <div id="popup-wrapper">
        <div class="alert alert-info" id="modulePopUp">
            <br>
            <div>
                <a class="pull-right glyphicon glyphicon-remove" id="closeModuleList"></a>
                <label>Modules:</label>
                <select id="listOfModules" class="form-control">
                    @foreach($allModules as $module)
                        <option value="{{ $module->id  }}"> {{ $module->module_name }}</option>
                    @endforeach
                </select>
            </div>

            <input type="button" class="btn btn-success" value="Join" id="selectModules"/></br>
            <div id="popUpModuleErrorMessage"></div>
        </div>
    </div>
</div>
<!-- hidden notification selection -->
@if (count($listDeclineModules) > 0)
    <div id="declineModule">
        <div id="popup-box">
            <div class="alert alert-danger">
                <a class="pull-right glyphicon glyphicon-remove" id="deleteDeclineModule"></a>
                @foreach($listDeclineModules as $module)
                    <h4>Your Request Module <b>{{$module->module_name}}</b> has been declined</h4>
                @endforeach
            </div>
        </div>
    </div>
@endif
<!-- Attendance notification -->
@if($role == 'tutor' && $firstChoiceModule != null)
    <div id="attendanceNotification">
        <div id="popup-wrapper">
            <div class="alert alert-info">
                <a class="pull-right glyphicon glyphicon-remove" id="deleteAttendanceNotification"></a>
                <div id="attendanceNotificationContent">

                </div>
            </div>
        </div>
    </div>
@endif