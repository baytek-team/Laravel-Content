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

<table class="ui selectable very basic table">
    <thead>
        <tr>
            <th>{{ ___('Title') }}</th>
            <th class="center aligned collapsing">{{ ___('Actions') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                {{-- {!! str_repeat('<i class="minus icon"></i>', $content->depth) !!}--}}
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

        {{-- @foreach($contents as $content)
            <tr>

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
        @endforeach --}}
    </tbody>
</table>

{{-- {{ $contents->links('pagination.default') }} --}}

@endsection