@extends('Content::admin')

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
    <a class="ui primary button" href="{{ route('content.create') }}">
        <i class="add icon"></i> {{ ___('Add content') }}
    </a>
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
                {{-- <th class="center aligned collapsing">{{ ___('ID') }}</th> --}}
                <!-- <th class="center aligned collapsing">{{ ___('Key') }}</th> -->
                <th>{{ ___('Title') }}</th>
                <th class="center aligned collapsing">{{ ___('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @php
                $relations = \Cache::get('content.cache.relations')->where('relation_type_id', 4);
                $loopies = \Baytek\Laravel\Content\Models\Content::loopying($contents, $relations, $contents);
            @endphp
            @foreach($loopies as $content)
                <tr>
                    {{-- <td class="collapsing">{{ $content->id }}</td> --}}
                    <!-- <td class="collapsing">{{ $content->key }}</td> -->
                    <td>
                        {{-- <a href="{{ route('content.show', $content->id) }}"> --}}
                            {!! str_repeat('<i class="minus icon"></i>', $content->depth) !!} {{ $content->title }}

                        {{-- </a> --}}
                    </td>
                    <td class="right aligned collapsing">
                        <div class="ui text compact menu">
                            {{-- <a href="{{ url($content->getUrl()) }}" class="item"><i class="world icon"></i>Visit</a> --}}
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