<!-- <!DOCTYPE html> -->
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="/css/all.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>

{{-- @include('app.common.navigation.sidebar') --}}


    {{-- @include('app.common.header.app') --}}

    {{-- @include('app.common.errors') --}}

    <div id="app" class="ui container">
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

    {{-- @include('app.common.footer') --}}

    <!--[if lte IE 10]>
    <script src="/js/dataset-shim.js"></script>
    <![endif]-->

    <!-- Scripts -->
    <!-- <script src="/wp-includes/js/jquery/jquery.js?ver=1.12.4"></script> -->
    <!-- <script src="/vendor/bundle.js"></script> -->
    <script src="/js/all.js"></script>

    <script>
        ;+function($){
            $('.ui.dropdown').dropdown();
            $('.button').popup();
            $('.ui.progress').progress();
            $('.menu .item').tab();

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
