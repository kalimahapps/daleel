@foreach ($namespace_data as $key => $data)
    <div class='flex justify-center'>
        @include('parts.link', [
            'href' => $data['link'],
            'label' => $data['label'],
            'extra_classes' => 'truncate w-full',
            'target' => '_self',
        ])
    </div>

    @if (!empty($data['namespaces']))
        <div>
            @include('parts.namespace', [
                'namespace_data' => $data['namespaces'],
            ])
        </div>
    @endif
@endforeach
