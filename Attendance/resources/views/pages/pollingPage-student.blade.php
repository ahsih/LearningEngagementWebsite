@extends('layouts.otherPage') @section('pageContent')
    <div class="container lightBlue">
        <div class="panel panel-heading">
            <h4><strong>Your Response:</strong></h4>
            <div class="panel-body">
                @if($responses != null)
                    @foreach($responses as $response)
                        <p class="responseQuestion noMarginBottom "><b>{{ $response->question->question }}</b>
                            Module:{{ $response->question->modules->module_name }}</p>
                        <p class="textNormal noMarginBottom">
                            <b>{{ $response->optionalAnswers->optional_answer }}</b> @if($response->question->correct_id == 0)
                                <b class="glyphicon glyphicon-info-sign"></b>
                            @elseif($response->question->correct_id == $response->optionalAnswers->id)
                                <b class="glyphicon glyphicon-ok greenIcon"></b>
                            @else
                                <b class="glyphicon glyphicon-remove redIcon"></b>
                            @endif</p>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@stop