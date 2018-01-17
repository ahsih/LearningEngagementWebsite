<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" href="{{asset('css/app.css')}}">
	<title>Learning Engagement</title>
</head>

<body>
		<h1 class="text-center">Learning Engagement</h1>
		@include('inc.navbar') 
		@yield('reward') 
		@yield('live-chat') 
		@yield('polling feature')
		@yield('notifications')
	</div>
	<!-- Scripts -->
	<script src="{{ asset('js/app.js') }}"></script>
</body>

</html>