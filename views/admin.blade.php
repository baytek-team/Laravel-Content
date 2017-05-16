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

    <!-- Main Quill library -->
    <script src="//cdn.quilljs.com/1.2.4/quill.min.js"></script>

    <!-- Theme included stylesheets -->
    <link href="//cdn.quilljs.com/1.2.4/quill.snow.css" rel="stylesheet">
    <link href="//cdn.quilljs.com/1.2.4/quill.bubble.css" rel="stylesheet">


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

                    @link(___('Site Index') , [
                        'class' => 'item collapsable',
                        'prepend' => '<i class="globe icon"></i>',
                        'location' => '/app'
                    ])

                    {{-- <a class="item collapsable">
                        <i class="globe icon"></i>
                        {{ ___('Site Index') }}
                    </a> --}}
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
                    @if( Auth::user() )
                        @link(___('Dashboard'), [
                            'location' => 'admin.index',
                            'append' => '<i class="dashboard icon"></i>',
                            'type' => 'route',
                            'class' => 'item'
                        ])
                        @if(Auth::user()->hasRole( \Baytek\Laravel\Users\Roles\Root::ROLE ))
                            <div class="item" href="{{ route('webpage.index') }}">
                                <i class="world icon"></i>
                                {{ ___('Webpages') }}
                                <div class="menu">
                                    @link(___('Create Webpage'), [
                                        'location' => 'webpage.create',
                                        'type' => 'route',
                                        'class' => 'item'
                                    ])
                                    @link(___('Manage Webpages'), [
                                        'location' => 'webpage.index',
                                        'type' => 'route',
                                        'class' => 'item'
                                    ])
                                </div>
                            </div>
                            <div class="item" href="{{ route('menu.index') }}">
                                <i class="sitemap icon"></i>
                                {{ ___('Menus') }}
                                <div class="menu">
                                    @link(___('Create Menu'), [
                                        'location' => 'menu.create',
                                        'type' => 'route',
                                        'class' => 'item'
                                    ])
                                    @link(___('Manage Menus'), [
                                        'location' => 'menu.index',
                                        'type' => 'route',
                                        'class' => 'item'
                                    ])
                                </div>
                            </div>
                            <div class="item" href="{{ route('settings.index') }}">
                                <i class="settings icon"></i>
                                {{ ___('Settings') }}
                                <div class="menu">
                                    @link(___('General Settings'), [
                                        'location' => 'settings.index',
                                        'type' => 'route',
                                        'class' => 'item'
                                    ])
                                </div>
                            </div>
                            <div class="item">
                                <i class="configure icon"></i>
                                {{ ___('Tools') }}

                                <div class="menu">
                                    @can('create', \Baytek\Laravel\Content\Models\Content::class)
                                        @link(___('Content Navigator'), [
                                            'location' => 'content.index',
                                            'type' => 'route',
                                            'class' => 'item'
                                        ])
                                    @endcan
                                </div>
                            </div>
                            @if(Auth::user()->hasRole( \Baytek\Laravel\Users\Roles\Root::ROLE ))
                                <div class="item">
                                    <i class="user icon"></i>
                                    {{ ___('Users') }}
                                    <div class="menu">
                                        @link(___('Manage Users'), [
                                            'location' => 'user.index',
                                            'type' => 'route',
                                            'class' => 'item'
                                        ])
                                        @link(___('Roles'), [
                                            'location' => 'role.index',
                                            'type' => 'route',
                                            'class' => 'item'
                                        ])
                                        @link(___('Permissions'), [
                                            'location' => 'permission.index',
                                            'type' => 'route',
                                            'class' => 'item'
                                        ])
                                        @link(___('Permission Matrix'), [
                                            'location' => 'user.role.index',
                                            'type' => 'route',
                                            'class' => 'item'
                                        ])
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
                                @yield('page.head.header', '<h1>Default Admin Dashboard</h1>')
                            </div>
                            <div class="right floated column" style="text-align: right">
                                @yield('page.head.menu')
                            </div>
                        </div>
                    </div>
                    <div class="ui hidden divider"></div>

                    <div class="ui compact segment">
                        <div class="ui breadcrumb">
                            @php
                                $folders = explode('/', Route::getCurrentRoute()->uri());
                                // dd(Route::getCurrentRoute());
                                // foreach($folders as $folder) {
                                //     // if($folder == )
                                // }

                                $path = '/';
                            @endphp

                            @foreach($folders as $index => $folder)
                                @php
                                    $path .= $folder . '/';
                                @endphp

                                @if(count($folders) != $index + 1)
                                    @link(___(title_case($folder)), [
                                        'location' => $path,
                                        'type' => 'url',
                                        'class' => 'section'
                                    ])
                                    <div class="divider"> / </div>
                                @else
                                    <div class="active section">{{___(title_case($folder))}}</div>
                                @endif
                            @endforeach

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

                    <div class="ui container" @if (!isset($notifications) || $notifications->count() == 0) style="display: none" @endif>
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
                    @if(trim($__env->yieldContent('content')))
                        @yield('content')
                    @else
                        <p>Use the menu to the left to navigate to useful administration features.</p>
                        <p>
                            <strong>Admin Dashboard Setup</strong>.<br/>
                            Check the <code>web.php</code> file for a route to <code>/admin</code> and change the view to a more desirable view.
                        </p>
                        <p>
                            <strong>Admin Controller</strong>.<br/>
                            You can also create an admin controller and create a dashboard method.
                        </p>
                    @endif
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
