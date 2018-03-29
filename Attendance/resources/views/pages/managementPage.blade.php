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
                <h4 class="text-info text-center">Change your module for modification</h4>
                <select class="form-control" id="liveChatModuleID">
                    <optgroup label="Modules">
                        @foreach ($modules as $module)
                            <option value="{{ $module->id  }}"> {{ $module->module_name }}</option>
                        @endforeach
                    </optgroup>
                </select>
                <input type="button" id="changeModule" value="Submit" class="btn btn-success center-block"/>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="text-center titleText">Modify students in your
                        module: <b class="text-danger">{{ $moduleName }}</b></h3
                </div>
                @if(Session::has('fileError'))
                    <p class="noMarginBottom text-danger"><b>{{ Session::get('fileError') }}</b></p>
                @endif
                {{ Session::forget('fileError') }}
                @if(Session::has('fileSuccess'))
                    <p class="noMarginBottom text-success"><b>{{ Session::get('fileSuccess') }}</b></p>
                @endif
                {{ Session::forget('fileSuccess') }}
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <h4 class="text-danger text-center">Add new students to the module</h4>
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
                    <br>
                    <hr>
                    {!! Form::open(array(
                    'url' => '/addListOfStudents',
                    'files' => true
                    )) !!}
                    {{ csrf_field() }}
                    <input type="file" name="file"/>
                    <button class="btn btn-success" id='uploadFile'>Upload list of student emails to be added
                    </button>
                    {!! Form::close() !!}
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <h4 class="text-danger text-center">Delete existing students from the current module</h4>
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
        </div>

    @endif
@stop