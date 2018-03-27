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
<h1 class="text-center titleText">{{ $title }}</h1>
@include('inc.navbar')
@yield('pageContent')
</body>
</html>