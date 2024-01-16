<!-- navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="{{url('/')}}"> <img src="{{getFile(config('location.logoIcon.path').'logo.png')}}" alt="{{config('basic.site_title')}}" /></a>
        <button
            class="navbar-toggler p-0"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="far fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{Request::routeIs('home') ? 'active' : ''}}" href="{{route('home')}}">@lang('Home')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{Request::routeIs('about') ? 'active' : ''}}" href="{{route('about')}}">@lang('About Us')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{Request::routeIs('property') ? 'active' : ''}}" href="{{route('property')}}">@lang('Property')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{Request::routeIs('blog') ? 'active' : ''}}" href="{{route('blog')}}">@lang('Blogs')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{Request::routeIs('faq') ? 'active' : ''}}" href="{{route('faq')}}">@lang('FAQ')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{Request::routeIs('contact') ? 'active' : ''}}" href="{{route('contact')}}">@lang('Contact')</a>
                </li>

                @guest
                    <li class="nav-item">
                        <a class="nav-link {{Request::routeIs('login') ? 'active' : ''}}" href="{{ route('login') }}">@lang('LOGIN')</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('user.home')}}">@lang('Dashboard')</a>
                    </li>
                @endguest
            </ul>
        </div>

    </div>
</nav>
