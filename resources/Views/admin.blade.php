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
    <div class="ui inverted vertical masthead center aligned segment">
        <div class="ui container inverted">
            <div class="ui large secondary inverted pointing menu">
                <div class="item">
                    <img src="http://cdn4.baytek.ca/wp-content/themes/baytek2016/images/logos/baytek-logo.svg" alt="" class="logo" scale="0">
                </div>
                {{-- <a class="toc item">
                    <i class="sidebar icon"></i>
                </a> --}}
                <a class="item">Home</a>
                <div class="ui dropdown item">
                    Users <i class="dropdown icon"></i>
                    <div class="menu">
                        <a class="item" href="{{ route('user.index') }}">Users</a>
                        <a class="item" href="{{ route('roles.index') }}">Roles</a>
                    </div>
                </div>
                <div class="ui dropdown item">
                    Content
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        <a href="{{ route('content.index') }}" class="item">Contents</a>
                        <a href="{{ route('webpage.index') }}" class="item">Webpages</a>
                        <a class="item">Menus</a>
                        <!-- <div class="item">Blog</div> -->
                        <!-- <div class="item">Events</div> -->
                        <!-- <div class="item">Forum</div> -->
                    </div>
                </div>
                <a class="item" href="{{ route('user.index') }}">Profile</a>
                <a class="item" href="{{ route('settings.index') }}">Settings</a>
                <a class="item" href="{{ route('settings.index') }}">Login</a>
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

    <!-- Scripts -->
    <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
    <script src="/js/all.js"></script>
    <!-- <script src="http://192.168.2.25:1337/pretzel.js"></script> -->

    <script>
        ;+function($){
            $('.ui.dropdown').dropdown({
                    onChange: function(value) {
                        var target = $(this).parents('.ui.dropdown');
                        if(value) {
                             target.find('.dropdown.icon').removeClass('dropdown').addClass('delete').on('click', function() {
                                target.dropdown('clear');
                                $(this).removeClass('delete').addClass('dropdown');
                            });
                        }
                    }
                });

            $('.button').popup();
            $('.ui.progress').progress();
            $('.menu .item').tab();
            $('.ui.checkbox').checkbox();
            $('.ui.radio.checkbox').checkbox();

            function UiCloneRow() {
                isEmpty = $(this).parent().find('input').filter(function() {
                    return !this.value;
                }).length;

                if(isEmpty) {
                    return false;
                }

                result = $(this).parent().clone(true, false).insertAfter($(this).parent());

                result.find('input').val('');

                result.find('.ui.dropdown').dropdown();

                $(this).parent()
                    .find('.add-row')
                        .addClass('negative')
                        .addClass('remove-row')
                        .removeClass('add-row')
                        .removeClass('positive')
                    .find('i.icon')
                        .addClass('remove')
                        .removeClass('add')
            }

            function UiRemoveRow() {
                $(this).parent().remove();
            }

            $('body').on('click', '.add-row', UiCloneRow);
            $('body').on('click', '.remove-row', UiRemoveRow);
        }(window.jQuery);
    </script>

    @yield('scripts')

    @if(isset($validation)) {!! $validation !!} @endif

</body>
</html>
