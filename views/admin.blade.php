<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pretzel CAS :: Content Authoring System') }}</title>

    <!-- Styles -->
    {{-- <link href="/css/all.css" rel="stylesheet"> --}}
    <link href="/css/app.css" rel="stylesheet">
    <link href="/semantic.min.css" rel="stylesheet">

    @yield('head')

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>;
        window.backend = true;
    </script>
</head>
<body>
    <div class="ui two column padded grid container">
        <div class="row">

            <div class="ui sixteen wide column">
                <div id="primary-navigation" class="ui top menu">
                    <a id="menu-toggle" class="item">
                        <i class="content icon"></i>
                    </a>

                    <a class="item collapsable">
                        <i class="globe icon"></i>
                        {{ ___('Site Index') }}
                    </a>
                    <div class="item collapsable">
                        <div class="ui icon input">
                            <input type="text" placeholder="{{ ___('Search...') }}">
                            <i class="search icon"></i>
                        </div>
                    </div>
                    <div class="right menu collapsable">
                        <a class="item" href="/{{ strtolower( ___('Fr') ) }}">
                            <i class="random icon"></i>
                            {{ ___('Switch Language') }}
                        </a>
                        @if(\Auth::user())
                            <div class="ui dropdown item">
                                <i class="user icon"></i>
                                {{ ___('My Profile') }}
                                <i class="dropdown icon"></i>
                                <div class="menu">

                                    <a class="item" href="{{ route('user.edit', \Auth::user()->id) }}">
                                        <i class="user icon"></i>
                                        {{ ___('Edit Profile') }}
                                    </a>
                                    @link(___('Logout'), [
                                        'method' => 'post',
                                        'location' => 'logout',
                                        'type' => 'route',
                                        'class' => 'item action',
                                        'prepend' => '<i class="sign out icon"></i>',
                                    ])
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div id="main-content" class="row">
            <div id="secondary-navigation" class="three wide column">
                <div class="ui left vertical fluid pointing stackable menu">
                    {{-- <div class="item">
                        <img class="ui small image" src="/img/cop_icon_white.svg" alt="" scale="0">
                    </div> --}}
                    {{-- <a class="item" href="/app/resources">
                        <i class="desktop icon"></i>
                        {{ ___('Public Application') }}
                    </a>
                    <a class="item" href="{{ route('admin.dashboard') }}">
                        <i class="dashboard icon"></i>
                        {{ ___('Admin Dashboard') }}
                    </a> --}}
                    @if( Auth::user() )
                        {{-- @can('View Member')
                            <a class="item" href="{{ route('members.index') }}">
                                <i class="user group icon"></i>
                                {{ ___('Members') }}
                            </a>
                            <div class="menu">
                                <a class="item" href="{{ route('members.edit', \Auth::user()->id) }}">{{ ___('My Profile') }}</a>
                                @if(Auth::user()->hasRole([\Baytek\Laravel\Users\Roles\Root::ROLE, \Baytek\Laravel\Users\Roles\Administrator::ROLE]))
                                    <a class="item" href="{{ route('members.adminindex') }}">{{ ___('Administrators') }}</a>
                                @endif
                            </div>
                            <div class="ui hidden divider"></div>
                        @endcan
                        @cannot('View Member')
                            <a class="item" href="{{ route('members.edit', \Auth::user()->id) }}">
                                <i class="user group icon"></i>
                                {{ ___('My Profile') }}
                            </a>
                        @endcannot --}}
                        @if(Auth::user()->hasRole( \Baytek\Laravel\Users\Roles\Root::ROLE ))
                            <div class="item" href="{{ route('webpage.index') }}">
                                <i class="world icon"></i>
                                {{ ___('Webpages') }}
                                <div class="menu">
                                    <a class="item" href="{{ route('webpage.create') }}">{{ ___('Create Webpage') }}</a>
                                    <a class="item" href="{{ route('webpage.index') }}">{{ ___('Manage Webpages') }}</a>
                                </div>
                            </div>
                            <div class="item" href="{{ route('menu.index') }}">
                                <i class="sitemap icon"></i>
                                {{ ___('Menus') }}
                                <div class="menu">
                                    <a class="item" href="{{ route('menu.create') }}">{{ ___('Create Menu') }}</a>
                                    <a class="item" href="{{ route('menu.index') }}">{{ ___('Manage Menus') }}</a>
                                </div>
                            </div>
                            <div class="item" href="{{ route('settings.index') }}">
                                <i class="settings icon"></i>
                                {{ ___('Settings') }}
                                <div class="menu">
                                    <a class="item" href="{{ route('settings.index') }}">{{ ___('General Settings') }}</a>
                                </div>
                            </div>

                            <div class="item">
                                <i class="configure icon"></i>
                                {{ ___('Tools') }}

                                <div class="menu">
                                    @can('create', \Baytek\Laravel\Content\Models\Content::class)
                                        <a class="item" href="{{ route('content.index') }}">{{ ___('Content Navigator') }}</a>
                                    @endcan
                                </div>
                            </div>
                            @if(Auth::user()->hasRole( \Baytek\Laravel\Users\Roles\Root::ROLE ))
                            <div class="item">
                                <i class="user icon"></i>
                                {{ ___('Users') }}
                                <div class="menu">
                                    <a class="item" href="{{ route('user.index') }}">{{ ___('Manage Users') }}</a>
                                    <a class="item" href="{{ route('role.index') }}">{{ ___('System Roles') }}</a>
                                    <a class="item" href="{{ route('permission.index') }}">{{ ___('System Permissions') }}</a>
                                    <a class="item" href="{{ route('user.role.index') }}">{{ ___('Permissions Matrix') }}</a>
                                </div>
                            </div>
                            @endif
                        @endif

                    @else
                        <a class="item" href="{{ route('login') }}">{{ ___('Login') }}</a>
                        <a class="item" href="{{ route('register') }}">{{ ___('Register') }}</a>
                    @endif
                </div>
            </div>
            <div class="thirteen wide column">
                {{-- <div class="ui container"> --}}
                    <div class="ui grid">
                        <div class="two column row">
                            <div class="left floated column">
                                @yield('page.head.header')
                            </div>
                            <div id="app" class="">
                                @yield('page.head.menu')
                            </div>
                        </div>
                    </div>
                    <div class="ui hidden divider"></div>
                    {{-- <div class="ui hidden divider"></div> --}}

                    @if (count($errors) > 0)
                        <div class="ui container">
                            <div class="ui hidden divider"></div>
                            <div class="row">
                                <div class="ui icon message error">
                                    <i class="exclamation circle icon"></i>
                                    <div class="content">
                                        <div class="header">{{ ___('Application Level Error') }}</div>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class=" ui container" @if (!isset($notifications) || $notifications->count() == 0) style="display: none" @endif>
                        <div class="ui hidden divider"></div>
                        <div class="row">
                            <div class="ui icon message">
                                <i class="exclamation circle icon"></i>
                                <div class="content">
                                    <div class="header">{{ ___('Notifications') }}</div>
                                    @if (isset($notifications) && $notifications->count() > 0)
                                        @foreach ($notifications as $notification)
                                            <li>{!! $notification->data['message'] !!}</li>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="ui hidden divider"></div>
                        <div class="ui hidden divider"></div>
                    </div>
                    @yield('content')
                    <div class="ui hidden divider"></div>
                </div>
            {{-- </div> --}}
        </div>
    </div>


    <div class="ui basic force modal" style="display: none">
        <div class="ui icon header">
            <i class="hand paper icon"></i>
            <span class="message"></span>
        </div>
        <div class="content">
            <p>Default modal text.</p>
        </div>
        <div class="actions">
            <div class="ui red basic inverted cancel button">
                <i class="remove icon"></i>
                <span class="message">{{ ___('Cancel') }}</span>
            </div>
            <div class="ui primary ok button">
                <i class="checkmark icon"></i>
                <span class="message">{{ ___('Yes') }}</span>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    {{-- <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script> --}}
    <script src="/js/app.js"></script>
    @if(env('APP_ENV') == 'local')
        <script src="http://192.168.2.25:1337/pretzel.js"></script>
    @endif

    @yield('scripts')

    @if(isset($validation)) {!! $validation !!} @endif

</body>
</html>
