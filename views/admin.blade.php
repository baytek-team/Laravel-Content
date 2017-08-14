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
    <link href="/css/app.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/daterangepicker.min.css">
    <link rel="stylesheet" href="/admin-ui/semantic.css"/>

    @yield('head')

    <style>
        .segment.simple.padded
        {
            padding: 1em 2em 1em 1em !important;
        }
    </style>

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>;
        window.backend = true;
    </script>
</head>
<body class="admin ui equal width padded grid">
    <div class="stretched row not padded">
        <div id="secondary-navigation" class="three wide column">
            <div class="ui vertical fluid inverted compact menu">
                <img src="/images/btk_logo2017_white.svg" alt="" class="logo">

                @if( Auth::user() )
                    @link(___('Dashboard'), [
                        'location' => 'admin.index',
                        'append' => '</span>',
                        'prepend' => '<i class="dashboard left icon"></i><span class="collapseable-text">',
                        'type' => 'route',
                        'class' => 'item'
                    ])

                    @foreach($menu as $menuGroup)
                        @foreach($menuGroup->items as $item)
                        {{-- @can('View News') --}}
                            @link($item->title, [
                                'location' => $item->content,
                                'append' => $item->metadata('append'),
                                'prepend' => $item->metadata('prepend'),
                                'type' => $item->metadata('type'),
                                'class' => $item->metadata('class') . (explode('.', Route::getCurrentRoute()->getAction()['as'])[0] == explode('.', $item->content)[0] ? ' active': '')
                            ])
                        {{-- @endcan --}}
                        @endforeach
                    @endforeach

                    @if(Auth::user()->hasRole( \Baytek\Laravel\Users\Roles\Root::ROLE ))
                        @link(___('Settings'), [
                            'location' => 'settings.index',
                            'append' => '</span>',
                            'prepend' => '<i class="settings left icon"></i><span class="collapseable-text">',
                            'type' => 'route',
                            'class' => 'item'
                        ])

                        @link(___('Rootinator'), [
                            'location' => 'content.index',
                            'append' => '</span>',
                            'prepend' => '<i class="wizard left icon"></i><span class="collapseable-text">',
                            'type' => 'route',
                            'class' => 'item'
                        ])

                        {{-- <div class="item">
                            <i class="configure left icon"></i>
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
                        </div> --}}
                        <div class="item">
                            <i class="user left icon"></i>
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

        <div class="thirteen wide column not padded">
            <div class="header bar row">
                <div id="primary-navigation" class="ui secondary menu borderless">
                    {{-- <a id="menu-toggle" class="item">
                        <i class="content icon"></i>
                    </a> --}}

                    <div class="ui item breadcrumb">
                        @breadcrumbs()
                    </div>

                    <div class="right menu">
                        {{-- <a class="item" href="/{{ strtolower( ___('Fr') ) }}">
                            <i class="flag icon"></i>
                            {{ ___('Lang') }}
                        </a> --}}
                        @if(\Auth::user())
                            <div class="ui dropdown item">
                                <i class="user icon"></i>
                                {{ ___('Profile') }}
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

                        @link('' , [
                            'class' => 'ui huge yellow icon button',
                            'prepend' => '<i class="globe icon"></i>',
                            'location' => '/app'
                        ])
                    </div>
                </div>
            </div>

            <div class="main padded">
                @include('flash::message')

                @if (count($errors) > 0)
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
                    <div class="ui hidden divider"></div>
                @endif

                <div class="ui container" @if (!isset($notifications) || $notifications->count() == 0) style="display: none" @endif>

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
                </div>

                <div class="ui hidden divider"></div>

                <div class="ui bottom aligned stackable grid">
                    <div class="six wide column">
                        @yield('page.head.header')
                    </div>
                    <div class="right floated right aligned ten wide column">
                        @yield('page.head.menu')
                    </div>
                </div>
                <div class="clear"></div>

                <div class="ui hidden divider"></div>
                <div class="ui hidden divider"></div>

                @yield('outer-content')

                @hasSection('content')
                    <div class="ui segment main-content">
                        @yield('content')
                    </div>
                @endif
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

    <!-- Scripts -->
    {{-- <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script> --}}
    <script src="/js/app.js"></script>
    @if(env('APP_ENV') == 'local')
        <script
            data-enabled
            data-host="//vault.baytek.ca/"
            src="//vault.baytek.ca/analytics.js"></script>
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