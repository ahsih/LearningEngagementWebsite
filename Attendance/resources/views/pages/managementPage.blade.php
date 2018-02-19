@extends('layouts.otherPage') @section('pageContent')
    @if($permission == false)
        <p>You do not have the permission to view this page</p>
    @else
        <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="text-center titleText">Approve student request</h3></div>
        <div class="panel-body">
        </div>
        </div>
        </div>
    @endif
@stop