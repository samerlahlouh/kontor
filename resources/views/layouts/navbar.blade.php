<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="/#page-top">{{ __('layout_lng.edu_logo') }}</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            Menu
            <i class="fa fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav text-uppercase ml-auto">
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/#services">{{ __('layout_lng.services') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/#about">{{ __('layout_lng.about_us') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/#contact">{{ __('layout_lng.contact_us') }}</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        schools
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="/schools/1">{{ __('layout_lng.elementary') }}</a>
                        <a class="dropdown-item" href="/schools/2">{{ __('layout_lng.middle') }}</a>
                        <a class="dropdown-item" href="/schools/3">{{ __('layout_lng.high_school') }}</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger"></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger"></a>
                </li>
                
                @if (Auth::guest())
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="{{ route('login') }}">
                                {{ __('layout_lng.login') }}
                                <i class="fa fa-sign-in" aria-hidden="true"></i>
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="/user_settings">
                                    <i class="fa fa-cog" aria-hidden="true"></i>
                                    {{ __('layout_lng.settings') }}
                                </a>
                                @if(Auth::user()->type == 'admin' || Auth::user()->type == 'agent')
                                <a class="dropdown-item" href="{{ route('register') }}">
                                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                                    {{ __('layout_lng.add_user') }}
                                </a>
                                @endIf
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                                        {{ __('layout_lng.logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            </div>
                        </li>
                        
                    @endif
            </ul>
        </div>
    </div>
</nav>
<div class="top-space"></div>