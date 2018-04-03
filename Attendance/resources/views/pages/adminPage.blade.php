@extends('layouts.otherPage') @section('pageContent')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h2 class="module-bottom-zero margin-zero-top font-navy"><b> Change User Profile </b></h2>
            <div class="panel-body">
                {!! Form::open(['action' => 'ManagementController@createTutor']) !!}
                {!! Form::token() !!}
                <table class="table table-hover">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-center">Mark as tutor?</th>
                    </tr>
                    @if(sizeof($studentUsers) > 0)
                        @foreach($studentUsers as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td class="text-center"><input type="checkbox" name="{{ $student->id }}" value="true"/>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </table>
                <button type="submit" class="btn btn-success pull-right">Submit</button>
                {!! Form::close() !!}
                <br/>
                <br/>
                {!! Form::open(['action' => 'ManagementController@changeToStudent']) !!}
                {!! Form::token() !!}
                <table class="table table-hover">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="text-center">Mark as student?</th>
                    </tr>
                    @if(sizeof($tutorUsers) > 0)
                        @foreach($tutorUsers as $tutor)
                            <tr>
                                <td>{{ $tutor->name }}</td>
                                <td>{{ $tutor->email }}</td>
                                <td class="text-center"><input type="checkbox" name="{{ $tutor->id }}" value="true"/>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </table>
                <button type="submit" class="btn btn-success pull-right">Submit</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop