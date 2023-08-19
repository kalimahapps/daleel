@foreach ($sidebar_data as $sidebar_value)
    @if (isset($sidebar_value['link']))
        @php
            $extra_classes = 'truncate w-full my-2';
            if ($depth > 1) {
                $extra_classes .= ' pl-4 hover:border-l hover:border-sky-700 dark:hover:border-sky-300';
            }
            
            $is_active_route = KalimahApps\Daleel\BladeUtil::isActiveRoute($active_route, $sidebar_value['link']);
        @endphp

        @include('parts.link', [
            'href' => $sidebar_value['link'],
            'label' => $sidebar_value['label'],
            'extra_classes' => $extra_classes,
            'target' => '_self',
        ])
    @endif


    @if (isset($sidebar_value['items']))
        <div
            class="{{ $depth < 1 ? 'pt-2' : 'border-l border-zinc-200 dark:border-zinc-700' }} {{ $depth > 1 ? 'ml-4' : '' }}">
            @include('aside.sidebar', [
                'sidebar_data' => $sidebar_value['items'],
                'depth' => $depth + 1,
            ])
        </div>
    @endif
@endforeach
