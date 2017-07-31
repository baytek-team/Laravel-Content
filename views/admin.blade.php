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
    {{-- <link href="/semantic.admin.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.11/semantic.min.css"/>

    @yield('head')

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>;
        window.backend = true;
    </script>
</head>
<body class="admin" style="margin-top: 20px">
    <div class="ui container">

        <div class="ui grid">
            <div class="ui sixteen wide column">
                <div id="primary-navigation" class="ui menu">
                    <a id="menu-toggle" class="item">
                        <i class="content icon"></i>
                    </a>

                    @link(___('Site Index') , [
                        'class' => 'item collapsable',
                        'prepend' => '<i class="globe icon"></i>',
                        'location' => '/app'
                    ])

                    <div class="ui item breadcrumb">
                        @breadcrumbs()
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
                                    @link(___('Edit Profile'), [
                                        'location' => 'user.edit',
                                        'type' => 'route',
                                        'class' => 'item',
                                        'prepend' => '<i class="user icon"></i>',
                                        'model' => \Auth::user()->id
                                    ])
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
        <div id="main-content" class="ui two column grid">


            <div id="secondary-navigation" class="three wide column">
                <div class="ui vertical fluid menu">
                    @if( Auth::user() )
                        @link(___('Dashboard'), [
                            'location' => 'admin.index',
                            'append' => '</span><i class="dashboard icon"></i>',
                            'prepend' => '<span class="collapseable-text">',
                            'type' => 'route',
                            'class' => 'item'
                        ])

                        @can('View News')
                            <div class="item">
                                <i class="newspaper icon"></i>
                                <span class="collapseable-text">{{ ___('News') }}</span>

                                <div class="menu">
                                    @link(___('Create News'), [
                                        'location' => 'news.create',
                                        'type' => 'route',
                                        'class' => 'item'
                                    ])
                                    @link(___('Manage News'), [
                                        'location' => 'news.index',
                                        'type' => 'route',
                                        'class' => 'item'
                                    ])
                                    @can('View News Category')
                                    @link(___('Categories'), [
                                        'location' => 'news.category.index',
                                        'type' => 'route',
                                        'class' => 'item'
                                    ])
                                    @endcan
                                </div>
                            </div>
                        @endcan


                        {{-- @can('View Resource')
                            <div class="item">
                                <i class="file text icon"></i>
                                <span class="collapseable-text">{{ ___('Resources') }}</span>

                                <div class="menu">
                                    @can('View Category')
                                        @link(___('Browser'), [
                                            'location' => 'resource.folder.index',
                                            'type' => 'route',
                                            'class' => 'item'
                                        ])
                                    @endcan
                                </div>
                            </div>
                        @endcan --}}

                        @can('View Webpage')
                            <div class="item">
                                <i class="world icon"></i>
                                <span class="collapseable-text">{{ ___('Webpages') }}</span>
                                <div class="menu">
                                    @can('Create Webpage')
                                        @link(___('Create Webpage'), [
                                            'location' => 'webpage.create',
                                            'type' => 'route',
                                            'class' => 'item'
                                        ])
                                    @endcan
                                    @link(___('Manage Webpages'), [
                                        'location' => 'webpage.index',
                                        'type' => 'route',
                                        'class' => 'item'
                                    ])
                                </div>
                            </div>
                        @endcan

                        @can('View Menu')
                            <div class="item" href="{{ route('menu.index') }}">
                                <i class="sitemap icon"></i>
                                <span class="collapseable-text">{{ ___('Menus') }}</span>
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
                        @endcan

                        @can('View Member')
                            <div class="item">
                                <i class="user group icon"></i>
                                <span class="collapseable-text">{{ ___('Members') }}</span>
                                <div class="menu">
{{--                                     @can('Create Member')
                                        @link(___('Create Member'), [
                                            'location' => 'members.create',
                                            'type' => 'route',
                                            'class' => 'item'
                                        ])
                                    @endcan --}}
                                    @link(___('Manage Members'), [
                                        'location' => 'members.index',
                                        'type' => 'route',
                                        'class' => 'item'
                                    ])
                                    @if(Auth::user()->hasRole([\Baytek\Laravel\Users\Roles\Root::ROLE, \Baytek\Laravel\Users\Roles\Administrator::ROLE]))
                                        @link(___('Administrators'), [
                                            'location' => 'members.adminindex',
                                            'type' => 'route',
                                            'class' => 'item'
                                        ])
{{--                                         @link(___('Member Import'), [
                                            'location' => 'members.import',
                                            'type' => 'route',
                                            'class' => 'item'
                                        ]) --}}
                                    @endif
                                </div>
                            </div>
                        @endcan

                        @if(Auth::user()->hasRole( \Baytek\Laravel\Users\Roles\Root::ROLE ))
                            <div class="item" href="{{ route('settings.index') }}">
                                <i class="settings icon"></i>
                                <span class="collapseable-text">{{ ___('Settings') }}</span>
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
                                <span class="collapseable-text">{{ ___('Tools') }}</span>

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
                            <div class="item">
                                <i class="user icon"></i>
                                <span class="collapseable-text">{{ ___('Users') }}</span>
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

                    @else
                        <a class="item" href="{{ route('login') }}">{{ ___('Login') }}</a>
                        <a class="item" href="{{ route('register') }}">{{ ___('Register') }}</a>
                    @endif
                </div>
            </div>

            <div class="thirteen wide column">
                <div class="ui container">
                    <div class="one column row">
                        <div class="left floated column">
                            <span style="float: right">
                                @yield('page.head.menu')
                            </span>
                            <span style="float: left">
                                @yield('page.head.header')
                            </span>
                        </div>
                    </div>
                </div>

                @include('flash::message')

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

                </div>
                <div id="main-content">
                    <div class="ui hidden divider"></div>
                    @yield('content')
                </div>
                <div class="ui hidden divider"></div>
            </div>
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
    <style>
        #secondary-navigation.minimized {
            position: fixed;
            left: 10px;
        }
        #main-content.minimized {
            margin-left: 60px;
        }
        .admin .ui.vertical.menu .menu {
            display: none;
        }

        .admin .ui.vertical.menu > .item {
            cursor: pointer;
        }
    </style>

    <!-- Scripts -->
    {{-- <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script> --}}
    <script src="/js/app.js"></script>
    @if(env('APP_ENV') == 'local')
        {{-- <script src="http://192.168.2.25:1337/pretzel.js"></script> --}}
    @endif

    @yield('scripts')
    <script>
        if ($('.admin').length) {
            $('.admin .ui.vertical.menu .menu').hide();
            $('#secondary-navigation .menu div.item .active').parent().slideDown();

            $(function(){
                $('#secondary-navigation .menu div.item').on('click', function(e){
                    if(e.target.nodeName != "A") {
                        if($(this).find('.menu').is(':visible')) return false;
                        $('.admin .ui.vertical.menu .menu').slideUp();
                        $(this).find('.menu').slideToggle();
                    }
                });
            });
        }
    </script>

    @if(isset($validation)) {!! $validation !!} @endif

</body>
</html>