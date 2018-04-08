<nav class="navbar navbar-default navbar-static-top noMarginBottom">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <p class="text-primary">Learning Engagement</p>
            </a>
        </div>

        <ul class="nav navbar-nav navbar-left">
            <li class="{{ $path == '/' ? 'active' : '' }}{{ $path == 'home' ? 'active' : '' }}"><a class="navbar-brand"
                                                                                                   href="{{ url('/') }}">Home</a>
            </li>
            <li class="{{ $path == 'reward' ? 'active' : '' }}"><a class="navbar-brand" href="{{ url('/reward') }}">Reward</a>
            </li>
            <li class="{{ $path == 'polling' ? 'active' : '' }}"><a class="navbar-brand" href="{{ url('/polling') }}">Polling</a>
            </li>
            <li class="{{ $path == 'management' ? 'active' : '' }}"><a class="navbar-brand"
                                                                       href="{{ url('/management') }}">Module management
                    @if(Session::has('requestModulesCount'))
                        @if(Session::get('requestModulesCount') > 0)
                            <span class="text-danger glyphicon glyphicon-exclamation-sign"></span>
                        @endif
                    @endif
                </a></li>
            <li class="{{ $path == 'attendance' ? 'active' : '' }}"><a class="navbar-brand"
                                                                       href="{{ url('/attendance') }}">Attendance</a>
            </li>
        </ul>

        <!-- Right Side Of Navbar -->
        <ul class="nav navbar-nav navbar-right">
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
        </ul>
    </div>
    </div>
</nav>