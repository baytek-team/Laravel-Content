@extends('content::admin')

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
    <div class="ui secondary menu">
        <div class="right item">
            <a class="ui labeled item" href="{{ route('content.create') }}">
                <i class="add icon"></i> {{ ___('Add content') }}
            </a>
        </div>
    </div>
@endsection

@section('content')

@section('page.head.header')
    <h1 class="ui header">
        <i class="world icon"></i>
        <div class="content">
            Content Management
            <div class="sub header">Manage the Content content type.</div>
        </div>
    </h1>
@endsection

<div class="content">

    <table class="ui selectable compact table">
        <thead>
            <tr>
                <th>{{ ___('Title') }}</th>
                <th class="center aligned collapsing">{{ ___('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contents as $content)
                <tr>
                    <td>
                        {!! str_repeat('<i class="minus icon"></i>', $content->depth) !!} {{ $content->title }}
                    </td>
                    <td class="right aligned collapsing">
                        <div class="ui text compact menu">
                            <a href="{{ route('content.edit', $content->id) }}" class="item">
                                <i class="pencil icon"></i> {{ ___('Edit') }}
                            </a>
                            <a href="{{ route('content.destroy', $content->id) }}" class="item">
                                <i class="delete icon"></i> {{ ___('Delete') }}
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="ui hidden divider"></div>

{{-- {{ $contents->links('pagination.default') }} --}}

@endsection