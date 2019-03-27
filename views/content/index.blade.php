@extends('contents::admin')

@section('page.head.header')
    <h1 class="ui header">
        <i class="heartbeat icon"></i>
        <div class="content">
            Content:
            <input id="content-id-changer" type="text" value="{{ $content->id }}">
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
    #content-id-changer {
        display: inline;
        border: none;
        font-size: 20pt;
        background: none;
        width: fit-content;
    }
</style>

<h2 class="ui header">
    <div class="content">
        {{ $content->title }}
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

@if(isset($diff))
    @include('contents::content.partials.differences')
@endif

<div class="ui stackable grid equal width">
    <div class="column">
        @include('contents::content.partials.attributes')
    </div>
    @if($content->relations->count())
        <div class="column">
            @include('contents::content.partials.relations')
        </div>
    @endif
    @if($content->meta->count())
        <div class="column">
            @include('contents::content.partials.metadata')
        </div>
    @endif
    @if(!empty(config('cms.content.content')))
        <div class="column">
            <h4 class="ui horizontal divider header">
                <i class="settings icon"></i>
                {{ ___('Settings') }}
            </h4>
            <div class="ui segment">
                @php
                    dump(config('cms.content.content'));
                @endphp
            </div>
        </div>
    @endif
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

        <div class="ui hidden divider"></div>
            <div class="ui hidden divider"></div>
            <table class="ui very basic content table">


        @foreach($contents as $content)
            {{-- @if($content->relation_type_id != $type) --}}
                {{-- @if($type != 0)
                    </tbody>
                </table>
                @endif --}}
                @php
                    $type = $content->relation_type_id;
                @endphp

            {{-- @endif --}}
                {{-- <thead>
                    <tr>
                        <th>{{ $content->relation_type_id }}</th>
                        <th class="center aligned collapsing">{{ ___('Actions') }}</th>
                    </tr>
                </thead> --}}
                {{-- <tbody> --}}


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
            {{-- </tbody> --}}
        @endforeach

        </table>

    {{-- {{ $contents->links('pagination.default') }} --}}
    @endif
@endsection

@section('scripts')
<script>
    var waitingForTimeout;
    $('#content-id-changer')
        .on('focus', function() {
            this.select();
        })
        .on('change', function() {
            window.location = '/admin/content/' + this.value
        })
        .on('keyup', function() {
            var field = this;
            window.clearTimeout(waitingForTimeout)
            waitingForTimeout = window.setTimeout(function(){
                $(field).trigger('change');
            }, 1500)
        })

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
