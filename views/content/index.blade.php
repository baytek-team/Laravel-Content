@extends('contents::admin')

@section('page.head.header')
    <h1 class="ui header">
        <i class="world icon"></i>
        <div class="content">
            {{ ___('Content Management') }}
            <div class="sub header">{{ ___('Manage the content content type.') }}</div>
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

<table class="ui selectable very basic content table">
    <thead>
        <tr>
            <th>{{ ___('Title') }}</th>
            <th class="center aligned collapsing">{{ ___('Actions') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr class="row-template" data-depth="0">
            <td>
                {{-- {!! str_repeat('<i class="minus icon"></i>', $content->depth) !!}--}}
                {{-- <div class="ui checkbox">
                    <input type="checkbox" class="hidden" name="content[{{$content->id}}]">
                    <label>{{ $content->title }}</label>
                </div> --}}
                <a href="{{ route('content.children', $content->id) }}" class="dynamic-load item">
                    <i class="plus icon"></i>
                </a>
                <span class="title">{{ $content->title }}</span>
            </td>
            <td class="right aligned collapsing">
                <div class="ui text compact menu">
                    <a href="{{ route('content.edit', $content->id) }}" class="item">
                        <i class="pencil icon"></i>
                    </a>
                    <a href="{{ route('content.destroy', $content->id) }}" class="item">
                        <i class="delete icon"></i>
                    </a>
                </div>
            </td>
        </tr>
        @if(isset($contents))
            @foreach($contents as $content)
                <tr>
                    <td>
                        {{-- {!! str_repeat('<i class="minus icon"></i>', $content->depth) !!}--}}
                        <input type="checkbox" class="ui checkbox" name="content[{{$content->id}}]">
                        {{ $content->title }}
                    </td>
                    <td class="right aligned collapsing">
                        <div class="ui text compact menu">
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
        @endif
    </tbody>
</table>
{{-- {{ $contents->links('pagination.default') }} --}}

@endsection

@section('scripts')
<script>
    $('.dynamic-load').on('click', function(e){
        e.preventDefault();
        var row = $(this).parents('tr');
        row.find('td:first i.icon').removeClass('plus').addClass('minus');

        $.get($(this).attr('href')).done(function(data){
            $.each(data, function(index, item){
                var $template = $('.row-template').clone(true).removeClass('row-template');
                $template.find('a').each(function() {
                    $(this).attr('href', $(this).attr('href').replace('1', item.id));
                });

                $template.find('.title').text(item.title);
                var depth = $($template)[0].dataset.depth = parseInt($(row)[0].dataset.depth) + 1;
                $template.find('td:first').css({
                    paddingLeft: (depth * 20) + 'px'
                });
                $template.find('td:first i.icon').removeClass('minus').addClass('plus');
                $(row).after($template);
            });
        });

        return false;
    });
</script>
@endsection