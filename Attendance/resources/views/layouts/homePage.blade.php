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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    <title>Learning Engagement</title>
</head>

<body>
<h1 class="text-center titleText">Learning Engagement</h1>
@include('inc.navbar')
@include('inc.notifications')
@yield('module')
<div class="container lightBlue">
    <button class="center-block btn btn-primary" id="changeLiveChat">Change Main Module <span
                class="glyphicon glyphicon-transfer"></span></button>
    <div class="row">
        <div class="col-sm-10 col-md-6 col-lg-6">
            <!-- live chat -->
            <div class="panel panel-heading">
                <h2 class="module-bottom-zero font-navy">@if ($moduleName != null)
                        Group Chat Module: {{ $moduleName }}
                    @else You Do Not Have A Group Chat Module
                    @endif
                </h2>
                <br>
                <!-- chat message -->
                <div class="panel-body">
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
                                            @if($role == 'tutor')
                                                @if($conversation->users != null)
                                                    @if($conversation->users->hasRole('tutor'))
                                                        <!-- Tutor Role -->
                                                            <li>
                                                                <p class="pull-left noMarginBottom">
                                                                    <button type="submit"
                                                                            class="invisibleDeleteMessage glyphicon glyphicon-minus-sign set-red buttonWithoutButtonlayout"></button>
                                                                    <b class="glyphicon glyphicon-king"></b><b
                                                                            class="text-primary">{{ $conversation->fullName }}
                                                                        :</b><b
                                                                            class="text-danger">{{ $conversation->message }}</b>&nbsp;<small
                                                                            class="text-info">{{ $conversation->created_at }}</small>

                                                                </p>
                                                            </li>
                                                    @else
                                                        <!-- Student message -->
                                                            <li>
                                                                <p class="pull-left noMarginBottom">
                                                                    <button type="submit"
                                                                            class="invisibleDeleteMessage glyphicon glyphicon-minus-sign set-red buttonWithoutButtonlayout"></button>
                                                                    {{ $conversation->fullName }}
                                                                    :<b class="text-danger">{{ $conversation->message }}</b>&nbsp;<small
                                                                            class="text-info">{{ $conversation->created_at }}</small>
                                                                </p>
                                                            </li>
                                                    @endif
                                                @else
                                                    <!-- No role message -->
                                                        <li>
                                                            <p class="pull-left noMarginBottom">
                                                                <button type="submit"
                                                                        class="invisibleDeleteMessage glyphicon glyphicon-minus-sign set-red buttonWithoutButtonlayout"></button>
                                                                {{ $conversation->fullName }}
                                                                :<b class="text-danger">{{ $conversation->message }}</b>&nbsp;<small
                                                                        class="text-info">{{ $conversation->created_at }}</small>
                                                            </p>
                                                        </li>
                                                @endif
                                            @else
                                                <!-- If it's a student -->
                                                @if($conversation->users != null)
                                                    @if($conversation->users->hasRole('tutor'))
                                                        <!-- tutor message -->
                                                            <li>
                                                                <p class="pull-left noMarginBottom">
                                                                    <b class="glyphicon glyphicon-king"></b><b>{{ $conversation->fullName }}
                                                                        :</b><b
                                                                            class="text-danger">{{ $conversation->message }}</b>&nbsp;<small
                                                                            class="text-info">{{ $conversation->created_at }}</small>

                                                                </p>
                                                            </li>
                                                    @elseif(Auth::user()->id == $conversation->user_id)
                                                        <!-- student own message -->
                                                            <li>
                                                                <p class=" pull-left noMarginBottom">
                                                                    <button type="submit"
                                                                            class="studentDeleteMessage glyphicon glyphicon-minus-sign set-red buttonWithoutButtonlayout"></button>{{ $conversation->fullName }}
                                                                    :<b class="text-danger">{{ $conversation->message }}</b>
                                                                    &nbsp;<small
                                                                            class="text-info">{{ $conversation->created_at }}</small>
                                                                </p>
                                                            </li>
                                                    @else
                                                        <!-- another student message -->
                                                            <li>
                                                                <p class="pull-left noMarginBottom">{{ $conversation->fullName }}
                                                                    :<b class="text-danger">{{ $conversation->message }}</b>}&nbsp;<small
                                                                            class="text-info">{{ $conversation->created_at }}</small>
                                                                </p>
                                                            </li>
                                                    @endif
                                                @else
                                                    <!-- another student message -->
                                                        <li>
                                                            <p class="pull-left noMarginBottom">{{ $conversation->fullName }}
                                                                <b class="text-danger">:{{ $conversation->message }}</b>&nbsp;<small
                                                                        class="text-info">{{ $conversation->created_at }}</small>
                                                            </p>
                                                        </li>
                                                    @endif
                                                @endif
                                            </ul>
                                            {!! Form::close() !!}
                                        @endforeach
                                    @endif
                                </div>
                                <label class="label-font-big"> Send Text </label>
                                @if ($moduleName != null)
                                    <input type="text" id="sendTextChat"
                                           placeholder="Message here"
                                           class="moduleBoxLarge"/>
                                    <input type="button" id="SendText" value="Send Message" class="btn btn-primary"/>
                                @else
                                    <input type="text" id="sendTextChat"
                                           placeholder="Type the message you want to send here"
                                           class="moduleBoxLarge" disabled/>
                                    <input type="button" id="SendText" value="Send Message" class="btn btn-primary"
                                           disabled/>
                                @endif
                                <label class="label-font-big"><input type="checkbox"
                                                                     id="anonymousTick"/>Anonymous</label>
                                <label id="messageConfirmation">@if(Session::has('noPermissionToDelete'))
                                        {{ Session::get('noPermissionToDelete') }}
                                    @endif
                                    {{ Session::forget('noPermissionToDelete') }}
                                </label>
                        </div>
                </div>
            </div>
            <div class="col-sm-1 col-md-6 col-lg-6">
                @yield('polling feature')
            </div>
        </div>
    </div>
    @yield('reward')
</div>
</div>
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>