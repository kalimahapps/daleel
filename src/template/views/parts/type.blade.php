@php
    $output = [];
    $resolved_types = KalimahApps\Daleel\BladeUtil::resolveTypes($types, $namespaces_list);
    $output_type = $output_type ?? 'string';
@endphp

@foreach ($resolved_types as $type_data)
    @if ($type_data['kind'] === 'use')
        @php
            $output[] = KalimahApps\Daleel\BladeUtil::getTemplateContent('parts.link', [
                'href' => "{$type_data['link']}",
                'label' => $type_data['name'],
                'extra_classes' => 'inline',
            ]);
        @endphp
    @endif


    @if ($type_data['kind'] === 'php')
        @php
            $output[] = KalimahApps\Daleel\BladeUtil::getTemplateContent('parts.link', [
                'href' => $type_data['link'],
                'label' => $type_data['name'],
                'extra_classes' => 'inline',
                'target' => '_blank',
            ]);
        @endphp
    @endif

    @if ($type_data['kind'] === 'other')
        @php
            $output[] = "<span title='{$type_data['title']}'>{$type_data['name']}</span>";
        @endphp
    @endif
@endforeach

@if ($output_type === 'list')
    @php
        $output = '<li>' . implode('</li><li>', $output) . '</li>';
    @endphp
    {!! $output !!}
@else
    @php
        echo implode('|', $output);
    @endphp
@endif
