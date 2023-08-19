@foreach ($namespace_data as $namespace_key => $namespace_data)
    @php
        $is_active_route = KalimahApps\Daleel\BladeUtil::isActiveRoute($active_route, $namespace_data['link']);
        $path = $path ?? 'root';
    @endphp

    <div class='flex justify-center items-center'>
        @include('parts.link', [
            'href' => $namespace_data['link'],
            'label' => $namespace_data['label'],
            'extra_classes' => 'truncate w-full py-0.5',
            'target' => '_self',
        ])

        @if (count($namespace_data['children']) > 0)
            @include('parts.collapsible', [
                'target' => $path . '-' . $namespace_data['label'],
                'is_expanded' => $is_active_route ? 'true' : 'false',
            ])
        @endif
    </div>

    @if (count($namespace_data['children']) > 0)
        <div data-group='{{ $path }}-{{ $namespace_data['label'] }}'
            class='ml-4 data-[show=false]:hidden'
            data-show='{{ $is_active_route ? 'true' : 'false' }}'>
            @include('aside.namespace', [
                'namespace_data' => $namespace_data['children'],
                'path' => $path . '-' . $namespace_data['label'],
            ])
        </div>
    @endif
@endforeach
