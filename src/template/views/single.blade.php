@extends('base', ['breadcrumbs' => $breadcrumbs ?? []])
@section('content')
    {!! $content !!}

    @php
        $prev_link = \KalimahApps\Daleel\BladeUtil::getNavLink('prev');
        $next_link = \KalimahApps\Daleel\BladeUtil::getNavLink('next');
        $edit_link = \KalimahApps\Daleel\BladeUtil::getEditLink();
    @endphp

    <div class="mt-20"></div>
    @if ($edit_link)
        <a href="{{ $edit_link }}" class="link-color transition-all text-sm">
            <i class="bi bi-pencil-square"></i>
            <span class="pl-1">Edit this page</span>
        </a>
    @endif

    <div class="border-t border-color h-2 my-4"></div>
    <div class='grid gap-3 sm:grid-cols-2'>
        <div>
            @if ($prev_link !== false)
                <a href="{{ $prev_link['link'] }}"
                    class="border border-slate-300 rounded py-2 px-4 block hover:border-sky-700 transition-all h-full">
                    <div class='text-xs text-slate-500'>Next page</div>
                    <div>{{ $prev_link['label'] }}</div>
                </a>
            @endif
        </div>

        <div>
            @if ($next_link !== false)
                <a href="{{ $next_link['link'] }}"
                    class="text-right border-slate-300 border rounded py-2 px-4 block hover:border-sky-700 transition-all h-full">
                    <div class='text-xs text-slate-500'>Next page</div>
                    <div>{{ $next_link['label'] }}</div>
                </a>
            @endif
        </div>
    </div>
@stop
