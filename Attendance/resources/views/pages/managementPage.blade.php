@extends('layouts.otherPage') @section('pageContent')
    @if($permission == false)
        <p>You do not have the permission to view this page</p>
    @else
        <div class="container lightBlue">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="text-center titleText">Approve student request</h3></div>
                <div class="panel-body">
                    {!! Form::open(['action' => 'ManagementController@acceptRequest']) !!}
                    {!! Form::token() !!}
                    <table class="table table-hover">
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Module Name</th>
                            <th>Accept</th>
                            <th>Decline</th>
                        </tr>
                        @foreach($listApprovedModules as $module)
                            <tr>
                                <td>{{ $module->full_name }}</td>
                                <td>{{ $module->email }}</td>
                                <td>{{ $module->module_name }}</td>
                                <td><input type="radio"
                                           name="userID{{ $module->user_id }}moduleID{{ $module->module_id }}"
                                           value="accept"/></td>
                                <td><input type="radio"
                                           name="userID{{ $module->user_id }}moduleID{{ $module->module_id }}"
                                           value="false"/></td>
                            </tr>
                        @endforeach
                    </table>
                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="container lightBlue">
            <div class="panel panel-default">
                <h4 class="text-info">Change your module</h4>
                <select class="form-control" id="liveChatModuleID">
                    <optgroup label="Modules">
                        @foreach ($modules as $module)
                            <option value="{{ $module->id  }}"> {{ $module->module_name }}</option>
                        @endforeach
                    </optgroup>
                </select>
                <input type="button" id="changeModule" value="Submit" class="btn btn-success center-block"/>
                <div class="panel-heading"><h3 class="text-center titleText">Modify students in your
                        module: <b class="text-danger">{{ $moduleName }}</b></h3>
                </div>
                <div class="panel-body">
                    <h4>Add new students to the module</h4>
                    <div class="scrollable-module">
                        {!! Form::open(['action' => 'ManagementController@addStudentToModule']) !!}
                        {!! Form::token() !!}
                        <table class="table table-hover">
                            <tr>
                                <th>Student Name</th>
                                <th>Student Email</th>
                                <th>Add</th>
                            </tr>
                            @foreach($listNotInModuleStudentUsers as $user)
                                <tr>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td><input type="checkbox" name="{{$user->id}}" value="true"/></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                    {!! Form::close() !!}
                </div>
                <div class="panel-body">
                    <h4>Delete existing students from the current module</h4>
                    <div class="scrollable-module">
                        {!! Form::open(['action' => 'ManagementController@deleteStudentInModule']) !!}
                        {!! Form::token() !!}
                        <table class="table table-hover">
                            <tr>
                                <th>Student Name</th>
                                <th>Student Email</th>
                                <th>Delete</th>
                            </tr>
                            @foreach($listStudentInThisModule as $user)
                                <tr>
                                    <td>{{$user->name}}</td>
                                    <td>{{$user->email}}</td>
                                    <td><input type="checkbox" name="{{$user->id}}" value="true"/></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

    @endif
@stop