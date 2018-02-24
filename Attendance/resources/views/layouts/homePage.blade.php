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
<div class="container">
    <div class="panel panel-heading">
        <h2 class="module-bottom-zero">@if ($moduleName != null)
                Live-Chat-{{ $moduleName }}
            @else You have no live-chat module
            @endif
            <button class="pull-right btn btn-warning" id="changeLiveChat">Change Live-Chat Module <span
                        class="glyphicon glyphicon-asterisk"></span></button>
        </h2>
        <!-- chat message -->
        <div class="panel panel-body">
            <!-- Live chat -->
            <div class="scrollable-chat" id="live-chat-messages">
                @if($moduleName != null)
                    @foreach($conversations as $conversation)
                        <ul class="list-inline setToZero">
                            <li><p class="pull-left text-danger">{{ $conversation->fullName }}:</p></li>
                            <li><p class="pull-left text-success">{{ $conversation->message }}</p></li>
                            <li><p class="pull-left text-info">{{ $conversation->created_at }}</p></li>
                        </ul>
                    @endforeach
                @endif
            </div>
            <label class="label-font-big"> Send Text </label>
            <input type="text" id="sendTextChat" placeholder="Type the message you want to send here"
                   class="moduleBoxLarge"/>
            <input type="button" id="SendText" value="Send Message" class="btn btn-success"/>
            <label class="label-font-big"><input type="checkbox" id="anonymousTick"/>Anonymous</label>
            <label id="messageConfirmation"></label>
        </div>
    </div>
</div>
@yield('polling feature')
@yield('notifications')
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>