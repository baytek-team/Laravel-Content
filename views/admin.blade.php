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
    <link href="/css/admin/app.css" rel="stylesheet">

    <link href="/css/semantic.min.css" rel="stylesheet">

    @yield('head')

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div class="full height">
        <div class="toc">
            <div class="ui fluid vertical inverted menu ">
                <div class="item">
                    <img class="ui small image" src="/img/cop_icon_white.svg" alt="" scale="0">
                </div>
                <a class="item" href="/app/resources">
                    <i class="desktop icon"></i>
                    {{ ___('Public Application') }}
                </a>
                <a class="item" href="{{ route('admin.dashboard') }}">
                    <i class="dashboard icon"></i>
                    {{ ___('Admin Dashboard') }}
                </a>
                @if( Auth::user() )
                    {{-- <a class="item" href="{{ route('admin.dashboard') }}">
                        <i class="dashboard icon"></i>
                        Dashboard
                    </a> --}}
                    @can('View Member')
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
                    @endcannot



                    @if(Auth::user()->hasRole( \Baytek\Laravel\Users\Roles\Root::ROLE ))

                        {{-- <a class="item" href="{{ route('webpage.index') }}">
                            <i class="world icon"></i>
                            Webpages
                        </a> --}}
                        <a class="item" href="{{ route('settings.index') }}">
                            <i class="settings icon"></i>
                            {{ ___('Settings') }}
                        </a>
                        <div class="menu">
                            <a class="item" href="{{ route('menu.index') }}">{{ ___('Manage Menus') }}</a>
                            <a class="item" href="{{ route('settings.index') }}">{{ ___('General Settings') }}</a>
                        </div>
                        <div class="ui hidden divider"></div>
                        <div class="item">
                            <i class="configure icon"></i>
                            {{ ___('Tools') }}
                        </div>
                        <div class="menu">
                            @can('create', \Baytek\Laravel\Content\Models\Content::class)
                                <a class="item" href="{{ route('content.index') }}">{{ ___('Contents List') }}</a>
                            @endcan
                            @if(Auth::user()->hasRole( \Baytek\Laravel\Users\Roles\Root::ROLE ))
                                <a class="item" href="{{ route('user.index') }}">{{ ___('All Users') }}</a>
                            @endif
                            <a class="item" href="{{ route('role.index') }}">{{ ___('System Roles') }}</a>
                            <a class="item" href="{{ route('permission.index') }}">{{ ___('System Permissions') }}</a>
                            <a class="item" href="{{ route('user.role.index') }}">{{ ___('Permissions Matrix') }}</a>
                        </div>
                        <div class="ui hidden divider"></div>
                    @endif
                    <a class="item" href="/{{ strtolower( ___('Fr') ) }}">
                        <i class="random icon"></i>
                        {{ ___('Switch Language') }}
                    </a>

                    @link(___('Logout'), [
                        'method' => 'post',
                        'location' => 'logout',
                        'type' => 'route',
                        'class' => 'item action',
                        'prepend' => '<i class="sign out icon"></i>',
                    ])
                @else
                    <a class="item" href="{{ route('login') }}">{{ ___('Login') }}</a>
                    <a class="item" href="{{ route('register') }}">{{ ___('Register') }}</a>
                @endif
            </div>
        </div>
        <div class="content">
            <div class="ui container">
                <div class="ui hidden divider"></div>
                <div class="ui grid">
                    <div class="two column row">
                        <div class="left floated column">
                            @yield('page.head.header')
                        </div>
                        <div class="">
                            @yield('page.head.menu')
                        </div>
                    </div>
                </div>
                <div class="ui hidden divider"></div>
                <div class="ui hidden divider"></div>

                @if (count($errors) > 0)
                <div class="ui container">
                    <div class="ui hidden divider"></div>
                    <div class="row">
                        <div class="ui icon message error">
                            <i class="exclamation circle icon"></i>
                            <div class="content">
                                <div class="header">Application Level Error</div>
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
                                <div class="header">Notifications</div>
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
        </div>
    </div>

    <div class="ui basic force modal">
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
                <span class="message">Cancel</span>
            </div>
            <div class="ui primary ok button">
                <i class="checkmark icon"></i>
                <span class="message">Yes</span>
            </div>
        </div>
    </div>

    <script>
        window.backend = true;
    </script>

    <!-- Scripts -->
    {{-- <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script> --}}
    <script src="/js/all.js"></script>
    {{-- <script src="/js/dropzone.js"></script> --}}
    {{-- <script src="http://192.168.2.25:1337/pretzel.js"></script> --}}
    {{-- <script src="/js/dropzone.js"></script> --}}

    @yield('scripts')

    @if(isset($validation)) {!! $validation !!} @endif

</body>
</html>
