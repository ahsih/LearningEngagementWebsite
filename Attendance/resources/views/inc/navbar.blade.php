<nav class="navbar navbar-default navbar-static-top">
	<div class="container">
		<div class="navbar-header">

			<!-- Collapsed Hamburger -->
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
				<span class="sr-only">Toggle Navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>

			<!-- Branding Image -->
			<a class="navbar-brand" href="{{ url('/') }}">
				Attendance
			</a>
		</div>

		<ul class="nav navbar-nav navbar-left">
			<li class="navbar-brand">Reward</li>
			<li class="navbar-brand">Polling</li>
			<li><a class="navbar-brand" href="{{ url('/management') }}">Module management</a></li>
			<li class="navbar-brand">Help</li>
		</ul>

		<!-- Right Side Of Navbar -->
		<ul class="nav navbar-nav navbar-right">
			<!-- Authentication Links -->
			@guest
			<li>
				<a href="{{ route('login') }}">Login</a>
			</li>
			<li>
				<a href="{{ route('register') }}">Register</a>
			</li>
			@else
			<li>
				<a class="navbar-brand">
					{{ Auth::user()->name }}
				</a>
			</li>
			<li>
				<a class="navbar-brand" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
					Logout
				</a>

				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					{{ csrf_field() }}
				</form>
			</li>
			@endguest
		</ul>
	</div>
	</div>
</nav>