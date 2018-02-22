@extends('layouts.otherPage') @section('pageContent')
    @if($permission == false)
        <p>You do not have the permission to view this page</p>
    @else
        <div class="container">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="text-center titleText">Approve student request</h3></div>
                <div class="panel-body">
                    {!! Form::open(['action' => 'ManagementController@acceptRequest']) !!}
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
                                <td><input type="radio" name="userID{{ $module->user_id }}moduleID{{ $module->module_id }}" value="accept"/></td>
                                <td><input type="radio" name="userID{{ $module->user_id }}moduleID{{ $module->module_id }}" value="false"/></td>
                            </tr>
                        @endforeach
                    </table>
                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    @endif
@stop