<div class="border-b pb-4 max-w-[95%] border-color mb-6">
    @if (!empty($kind))
        @include('parts.tag', [
            'label' => $kind,
            'classes' => 'text-xs',
            'style' => 'info',
        ])
    @endif

    <span class='text-xs'>
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!$loop->last)
                @include('parts.link', [
                    'href' => $breadcrumb['link'],
                    'label' => $breadcrumb['label'],
                    'extra_classes' => 'inline',
                ])
                <span class="text-zinc-300">/</span>
            @else
                {{ $breadcrumb['label'] }}
            @endif
        @endforeach
    </span>
</div>
