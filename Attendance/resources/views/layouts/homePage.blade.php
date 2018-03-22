<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS -->
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/common.css')}}">
    <!-- Javascript for Ajax -->
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <!-- Javascript for chart js to create chart -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <title>Learning Engagement</title>
</head>

<body>
<h1 class="text-center titleText">Learning Engagement</h1>
@include('inc.navbar')
@include('inc.notifications')
@yield('reward')
@yield('module')
<div class="container lightBlue">
    <div class="panel panel-heading">
        <h2 class="module-bottom-zero font-navy">@if ($moduleName != null)
                Group Chat Module: {{ $moduleName }}
            @else You Do Not Have A Group Chat Module
            @endif
            <button class="pull-right btn btn-warning" id="changeLiveChat">Change Main Module <span
                        class="glyphicon glyphicon-asterisk"></span></button>
        </h2>
        <!-- chat message -->
        <div class="panel panel-body">
            <!-- Live chat -->
            @if($role == 'tutor')
                <div class="scrollable-chat conversationMessage" id="live-chat-messages">
                    @elseif($role == 'student')
                        <div class="scrollable-chat studentOwnMessage" id="live-chat-messages">
                            @endif

                            @if($moduleName != null)
                                @foreach($conversations as $conversation)
                                    {!! Form::open(['action'=>'ConversationController@deleteMessage']) !!}
                                    <input type="hidden" value="{{ $conversation->id }}" name="deleteValue"/>
                                    <ul class="list-inline setToZero">
                                        <li>
                                            @if($conversation->users != null)
                                                @if($conversation->users->hasRole('tutor'))
                                                    <p class="pull-left text-danger">
                                                        <b class="glyphicon glyphicon-king">{{ $conversation->fullName }}:</b></p>
                                                        @else
                                                    <p class="pull-left text-danger">{{ $conversation->fullName }}:</p>
                                                @endif
                                            @else
                                                <p class="pull-left text-danger">{{ $conversation->fullName }}:</p>
                                            @endif</li>
                                        <li><p class="pull-left text-success">{{ $conversation->message }}</p></li>
                                        <li><p class="pull-left text-info">{{ $conversation->created_at }}</p></li>
                                        @if($role == 'tutor')
                                            <li class="invisibleDeleteMessage pull-left text-info">
                                                <button type="submit"
                                                        class="glyphicon glyphicon-minus-sign set-red buttonWithoutButtonlayout"></button>
                                            </li>
                                        @elseif(Auth::user()->id == $conversation->user_id)
                                            <li class="studentDeleteMessage pull-left text-info">
                                                <button type="submit"
                                                        class="glyphicon glyphicon-minus-sign set-red buttonWithoutButtonlayout"></button>
                                            </li>
                                        @endif
                                    </ul>
                                    {!! Form::close() !!}
                                @endforeach
                            @endif
                        </div>
                        <label class="label-font-big"> Send Text </label>
                        @if ($moduleName != null)
                            <input type="text" id="sendTextChat" placeholder="Type the message you want to send here"
                                   class="moduleBoxLarge"/>
                            <input type="button" id="SendText" value="Send Message" class="btn btn-success"/>
                        @else
                            <input type="text" id="sendTextChat" placeholder="Type the message you want to send here"
                                   class="moduleBoxLarge" disabled/>
                            <input type="button" id="SendText" value="Send Message" class="btn btn-success" disabled/>
                        @endif
                        <label class="label-font-big"><input type="checkbox" id="anonymousTick"/>Anonymous</label>
                        <label id="messageConfirmation">@if(Session::has('noPermissionToDelete'))
                                {{ Session::get('noPermissionToDelete') }}
                            @endif
                            {{ Session::forget('noPermissionToDelete') }}
                        </label>
                </div>
        </div>
    </div>
</div>
@yield('polling feature')
@yield('notifications')
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>