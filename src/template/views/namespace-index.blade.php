@extends('base', ['kind' => 'namespace'])
@section('content')
    <h1>
        {{ $breadcrumbs[count($breadcrumbs) - 1]['label'] }}
    </h1>
    @foreach ($namespace_data as $data)
        <h2 class='mt-4 font-bold'>{{ $data['label'] }}</h2>
        @foreach ($data['children'] as $object_key => $object_data)
            @include('parts.link', [
                'href' => $object_data['link'],
                'label' => $object_data['label'],
            ])

            @if (!empty($object_data['children']))
                @include('parts.namespace', [
                    'namespace_data' => $object_data['children'],
                ])
            @endif
        @endforeach
    @endforeach
@stop
