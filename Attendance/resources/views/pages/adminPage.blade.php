@extends('layouts.otherPage') @section('pageContent')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h2 class="module-bottom-zero margin-zero-top font-navy"><b> Create Classroom polling</b></h2>
            <div class="panel-body">
                    {!! Form::open(['action' => 'ManagementController@createTutor']) !!}
                    <table class="table table-hover">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-center">Make as tutor?</th>
                        </tr>
                        @foreach($studentUsers as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td class="text-center"><input type="checkbox" name="{{ $student->id }}" value="true"/>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <button type="submit" class="btn btn-success pull-right">Submit</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop