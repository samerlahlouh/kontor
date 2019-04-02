<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="/#page-top">{{ __('layout_lng.edu_logo') }}</a>
        @if(Auth::user() && (Auth::user()->type == 'regular' || Auth::user()->type == 'agent'))
        <div>
            <div class="row-">
                <div class="col">
                    <h6 class="balance">
                        {{ __('layout_lng.balance') }}
                        <span>{{ Auth::user()->balance }}</span>
                    </h6>
                </div>
                <div class="col">
                    <h6  class="credit">
                        {{ __('layout_lng.credit') }}
                        <span>{{ Auth::user()->credit }}</span>
                    </h6>
                </div>
            </div>
        </div>
        @endIf
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            Menu
            <i class="fa fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav text-uppercase ml-auto">
                @if(!Auth::user())
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/#services">{{ __('layout_lng.services') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/#about">{{ __('layout_lng.about_us') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/#contact">{{ __('layout_lng.contact_us') }}</a>
                </li>
                @endIf

                @if(Auth::user() && Auth::user()->type == 'regular')
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/regular_packets">{{ __('layout_lng.packets') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/regular_chargings">{{ __('layout_lng.chargings') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/regular_orders">{{ __('layout_lng.orders') }}</a>
                </li>
                @endIf



                @if(Auth::user() && (Auth::user()->type == 'admin' || Auth::user()->type == 'agent'))
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/users">{{ __('layout_lng.users') }}</a>
                </li>
                @endIf

                @if(Auth::user() && (Auth::user()->type == 'agent'))
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/packets">{{ __('layout_lng.packets') }}</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="agentChargingsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('layout_lng.chargings') }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="agentChargingsDropdown">
                        <a class="dropdown-item" href="/regular_chargings">
                            <i class="fa fa-money" aria-hidden="true"></i>
                            {{ __('layout_lng.my_chargings') }}
                        </a>
                        <a class="dropdown-item" href="/chargings">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            {{ __('layout_lng.users') }}
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/regular_orders">{{ __('layout_lng.orders') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link js-scroll-trigger" href="/agent_transfer">{{ __('layout_lng.transfer') }}</a>
                </li>
                @endIf

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