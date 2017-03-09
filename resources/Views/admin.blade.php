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
    <link href="/css/all.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">

    @yield('head')

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div class="ui inverted vertical center aligned segment">
        <div class="ui container inverted">
            <div class="ui large secondary inverted pointing menu">
                <div class="item">
                    <img src="http://cdn4.baytek.ca/wp-content/themes/baytek2016/images/logos/baytek-logo.svg" alt="" class="logo" scale="0">
                </div>
                {{--
                    <a class="toc item">
                        <i class="sidebar icon"></i>
                    </a>
                --}}
                @if( Auth::user() )
                <a class="item">Home</a>
                <div class="ui dropdown item">
                    Users <i class="dropdown icon"></i>
                    <div class="menu">
                        <a class="item" href="{{ route('user.index') }}">Users</a>
                        <a class="item" href="{{ route('role.index') }}">Roles</a>
                        <a class="item" href="{{ route('user.role.index') }}">User Roles</a>
                    </div>
                </div>
                <div class="ui dropdown item">
                    Content
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        <a href="{{ route('content.index') }}" class="item">Contents</a>
                        <a href="{{ route('webpage.index') }}" class="item">Webpages</a>
                        <a href="{{ route('menu.index') }}" class="item">Menus</a>
                        <div class="item">Taxonomy</div>
                        <!-- <div class="item">Blog</div> -->
                        <!-- <div class="item">Events</div> -->
                        <!-- <div class="item">Forum</div> -->
                    </div>
                </div>
                <a class="item" href="{{ route('user.index') }}">Profile</a>
                <a class="item" href="{{ route('settings.index') }}">Settings</a>
                @else
                    <a class="item" href="{{ route('login') }}">Login</a>

                    <a class="item" href="{{ route('register') }}">Register</a>

                @endif
            </div>
        </div>
    </div>

    <div class="ui hidden divider"></div>
    <div class="ui hidden divider"></div>

    <div id="app" class="ui container">
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

        <!-- <div class=" ui container" @if (!isset($notifications) || $notifications->count() == 0) style="display: none" @endif>
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
        </div> -->
        @yield('content')
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

    <!-- Scripts -->
    {{-- <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script> --}}
    <script src="/js/all.js"></script>
    <!-- <script src="http://192.168.2.25:1337/pretzel.js"></script> -->

    <script>

    </script>

    @yield('scripts')

    <script src="/js/global.js"></script>

    @if(isset($validation)) {!! $validation !!} @endif

</body>
</html>
