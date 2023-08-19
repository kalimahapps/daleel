@extends('base', ['kind' => $function_data['kind']])
@section('content')
    <h1 class='m-0'>
        {{ $breadcrumbs[count($breadcrumbs) - 1]['label'] }}
    </h1>

    @include('parts.method', [
        'method_key' => $function_key,
        'method_data' => $function_data,
    ])
@stop
