@extends('base', ['kind' => $kind])
@section('content')

    <!-- Display flags (static, public, etc.) for the method. -->
    @if (!empty($flags))
        @include('parts.flags', [
            'flags' => $flags,
        ])
    @endif

    <h1 class='m-0'>
        {{ $breadcrumbs[count($breadcrumbs) - 1]['label'] }}
    </h1>

    @if (!empty($docblock))
        <div class='mb-8'>
            @if ($docblock['summary'])
                <div>
                    {!! $docblock['summary'] !!}
                </div>
            @endif
            @if ($docblock['description'])
                <div class='text-sm'>
                    {!! $docblock['description'] !!}
                </div>
            @endif
        </div>
    @endif

    @if (!empty($extends))
        <h4>Extends</h4>
        <ul class='text-sm'>
            @include('parts.type', ['types' => $extends])
        </ul>
    @endif

    @if (!empty($implements))
        <h4>Implements</h4>
        <ul class='text-sm'>
            @include('parts.type', [
                'types' => $implements,
                'output_type' => 'list',
            ])
        </ul>
    @endif

    @if (!empty($traits_uses))
        <h4>Uses</h4>
        <ul class='text-sm'>
            @include('parts.type', [
                'types' => $traits_uses,
                'output_type' => 'list',
            ])
        </ul>
    @endif

    <!-- Display see links from docblock. -->
    @if (!empty($docblock['tags']['see']))
        <h4>See</h4>
        @include('parts.see', [
            'data' => $docblock['tags']['see'],
        ])
    @endif

    @if (!empty($constants))
        <h2 id="Constants"
            class='!text-teal-700 dark:!text-teal-300 scroll-mt-20 relative group'>
            @include('parts.anchor', [
                'href' => 'Constants',
            ])
            Constants
        </h2>
        @include('parts.members', [
            'members' => $constants,
            'type' => 'constant',
            'colors' => 'text-teal-800 dark:text-teal-300',
        ])
    @endif

    @if (!empty($properties))
        <h2 id="Properties"
            class='!text-emerald-700 dark:!text-emerald-300 scroll-mt-20 relative group'>
            @include('parts.anchor', [
                'href' => 'Properties',
            ])
            Properties
        </h2>
        @include('parts.members', [
            'members' => $properties,
            'type' => 'property',
            'colors' => 'text-emerald-800 dark:text-emerald-300',
        ])
    @endif

    @if (!empty($methods))
        <h2 id="Methods"
            class='dark:!text-violet-400 !text-violet-900 scroll-mt-20 relative group'>
            @include('parts.anchor', [
                'href' => 'Methods',
            ])
            Methods
        </h2>
        @foreach ($methods as $method_key => $method_data)
            @include('parts.method', [
                'method_key' => $method_key,
                'method_data' => $method_data,
            ])
        @endforeach
    @endif
@stop
