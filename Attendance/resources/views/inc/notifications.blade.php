<!-- hidden div, which display the setting of the live chat -->
<div id="liveChatSettings" class="hidden-popup">
    <div id="popup-wrapper">
        <div class="alert alert-info">
            <a class="pull-right glyphicon glyphicon-remove" id="closeLiveChatSettings"></a>
            <h5> Change your live-chat module</h5>
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
<!-- hidden div, which display any notification -->
<div class="popup-boxes">
    <div id="popup-box">
        <div>
            <p> BITCONNECT</p>
            <p>sadasd</p>
        </div>
    </div>
</div>
<!-- Hidden module selection -->
<div id="moduleList" class="moduleList-popup">
    <div id="popup-wrapper">
        <div class="alert alert-info" id="modulePopUp">
            <a class="pull-right glyphicon glyphicon-remove" id="closeModuleList"></a>
            <select id="listOfModules" class="form-control">
                @foreach($allModules as $module)
                    <option value="{{ $module->id  }}"> {{ $module->module_name }}</option>
                @endforeach
            </select>
            <input type="button" class="btn btn-success" value="Submit" id="selectModules"/></br>
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