@extends('contents::admin')

@section('page.head.header')
    <h1 class="ui header">
        <i class="heartbeat icon"></i>
        <div class="content">
            Contentinator
            <div class="sub header">Contentination level: @php echo rand(0,100);@endphp%</div>
        </div>
    </h1>
@endsection

@section('page.head.menu')
    <div class="ui secondary contextual menu">
        <div class="item">
            <a class="ui primary button" href="{{ route('content.create') }}">
                <i class="add icon"></i> {{ ___('Add content') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<style>
    .table .nested.table
    {
        padding: 0;
    }
</style>

<h2 class="ui header">
    {{-- <i class="content icon"></i> --}}
    <div class="content">
        {{ $content->title }}
        {{-- <div class="sub header">{{ ___('Manage the content content type.') }}</div> --}}
    </div>
</h2>

@if(!empty($content->content))
    <h4 class="ui horizontal divider header">
        <i class="globe icon"></i>
        {{ ___('Content') }}
    </h4>
    <div class="ui basic segment" style='max-height: 400px; overflow: auto'>
        {!! $content->content !!}
    </div>
@endif

{{-- @if($diff)
    @include('contents::content.partials.differences')
@endif --}}

<div class="ui grid">
    <div class="four wide widescreen eight wide computer eight wide tablet sixteen wide mobile column">
        @include('contents::content.partials.attributes')
    </div>
    <div class="four wide widescreen eight wide computer eight wide tablet sixteen wide mobile column">
        @if($content->relations->count())
            @include('contents::content.partials.relations')
        @endif
    </div>
    <div class="four wide widescreen eight wide computer eight wide tablet sixteen wide mobile column">
        @if($content->meta->count())
            @include('contents::content.partials.metadata')
        @endif
    </div>
    <div class="four wide widescreen eight wide computer eight wide tablet sixteen wide mobile column">
        <h4 class="ui horizontal divider header">
            <i class="settings icon"></i>
            {{ ___('Settings') }}
        </h4>
        @if(!empty(config('cms.content.content')))
            <div class="ui segment">
                @php
                    dump(config('cms.content.content'));
                @endphp
            </div>
        @endif
    </div>
</div>

@if(!count($contents))
    <div class="ui divider"></div>
    <h2>Nothing relates to this content</h2>
@else
    <h4 class="ui horizontal divider header">
        {{-- <i class="globe icon"></i> --}}
        {{ ___('Content that relates to: ') . $content->key }}
    </h4>

        @php
            $type = 0;
            $close = false;
        @endphp

        @foreach($contents as $content)
            @if($content->relation_type_id != $type)
                @if($type != 0)
                    </tbody>
                </table>
                @endif
                @php
                    $type = $content->relation_type_id;
                @endphp
            <div class="ui hidden divider"></div>
            <div class="ui hidden divider"></div>
            <table class="ui very basic content table">
                <thead>
                    <tr>
                        <th>{{ content($content->relation_type_id)->title }}</th>
                        <th class="center aligned collapsing">{{ ___('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
            @endif


            <tr @if($contents->first() == $content) class="row-template" data-original-id="{{ $content->id }}" @endif data-depth="0" data-expanded="" >
                <td>
                    <a href="{{ route('content.children', $content->id) }}" class="dynamic-load item">
                        <i class="plus small icon"></i>
                    </a>
                    <a href="{{ route('content.list', $content->id) }}" class="title">
                        {{ $content->title }}
                    </a>
                </td>
                <td class="right aligned collapsing">
                    <div class="ui text compact menu">
                        <a href="{{ route('content.show', $content->id) }}" class="item">
                            <i class="eye icon"></i>
                        </a>
                        <a href="{{ route('content.edit', $content->id) }}" class="item">
                            <i class="pencil icon"></i>
                        </a>
                        <a href="{{ route('content.destroy', $content->id) }}" class="item">
                            <i class="delete icon"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
        </table>

    {{-- {{ $contents->links('pagination.default') }} --}}
    @endif
@endsection

@section('scripts')
<script>
    $('.dynamic-load').on('click', function(e){
        e.preventDefault();
        var row = $(this).parent().parent();
        row.find('td:first i.icon').removeClass('plus').addClass('minus');

        if($(row)[0].dataset.expanded === 'true') {
            $(row).next().hide();
            $(row)[0].dataset.expanded = false;
            $(row).find('td:first i.icon').removeClass('minus').addClass('plus');
        }
        else if($(row)[0].dataset.expanded === 'false'){
            $(row).next().show();
            $(row)[0].dataset.expanded = true;
            $(row).find('td:first i.icon').removeClass('plus').addClass('minus');
        }
        else {
            $(row)[0].dataset.expanded = true;

            $.get($(this).attr('href')).done(function(data){
                var nest = $('<tr><td colspan="2" class="nested table"><table class="ui very basic content table"></table></td></tr>');

                $.each(data, function(index, item){
                    var $template = $('.row-template').clone(true).removeClass('row-template');
                    $template.find('a').each(function() {
                        $(this).attr('href', $(this).attr('href').replace($(this)[0].dataset.originalId, item.id));
                    });

                    $template.find('.title').text(item.title);
                    var depth = $($template)[0].dataset.depth = parseInt($(row)[0].dataset.depth) + 1;
                    $template.find('td:first').css({
                        paddingLeft: (depth * 30) + 'px'
                    });
                    $template.find('td:first i.icon').removeClass('minus').addClass('plus');
                    delete($($template)[0].dataset.expanded);
                    $(nest).find('table').append($template);
                });

                $(row).after(nest);
            });
        }

        return;
    });
</script>
@endsection
