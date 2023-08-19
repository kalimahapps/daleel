<div
    class='text-sm flex flex-col pr-4 overflow-auto w-[220px] h-full pt-4 tablet:pt-2'>
    @include('parts.site-title', [
        'classes' => 'hidden tablet:flex',
    ])
    <div class='overflow-auto grow mb-4'>
        <div>
            @if (!empty($docs_sidebar))
                @foreach ($docs_sidebar as $sidebar_value)
                    @php
                        $should_expand_children = false;
                        if (isset($sidebar_value['items'])) {
                            $should_expand_children = KalimahApps\Daleel\BladeUtil::hasActiveChild($sidebar_value['items'], $active_route);
                        }
                    @endphp
                    <div class='pb-3 mb-3 border-b border-color'>
                        <h3 class='py-0.5 flex items-center'>
                            <span class="font-bold truncate w-full">
                                {{ $sidebar_value['label'] }}
                            </span>
                            @if (count($sidebar_value) > 0)
                                @include('parts.collapsible', [
                                    'target' => $sidebar_value['label'],
                                    'is_expanded' => $should_expand_children
                                        ? 'true'
                                        : 'false',
                                ])
                            @endif
                        </h3>
                        <div data-group='{{ $sidebar_value['label'] }}'
                            class='ml-4 data-[show=false]:hidden'
                            data-show='{{ $should_expand_children ? 'true' : 'false' }}'>
                            @include('aside.sidebar', [
                                'sidebar_data' => $sidebar_value['items'],
                                'depth' => 1,
                            ])
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        @if (!empty($api_sidebar))
            <div>
                <h3 class="font-bold pt-2">API</h3>
                @include('aside.namespace', [
                    'namespace_data' => $api_sidebar,
                ])
            </div>
        @endif
    </div>
    @php
        $versions = \KalimahApps\Daleel\BladeUtil::getConfig('versions');
        $versions_count = count(array_keys($versions));
    @endphp
    <div
        class='mt-auto text-center py-2 border-t border-color relative group {{ $versions_count > 1 ? 'cursor-pointer' : '' }}'>
        <spna>
            version: {{ $config['current_version'] }}
            @if ($versions_count > 1)
                <i class="bi bi-caret-up-fill text-zinc-400 text-[10px]"></i>
            @endif
        </spna>
        @if ($versions_count > 1)
            <div
                class="
            bottom-full absolute left-1/2 -translate-x-1/2 z-10 pt-2 pointer-events-none
            group-hover:pointer-events-auto transition-all opacity-0 group-hover:opacity-100 min-w-[50%]
            ">
                <div class="shadow-xl rounded border border-color main-bg">
                    @foreach ($versions as $version_key => $version_data)
                        @php($version_url = \KalimahApps\Daleel\BladeUtil::getVersionUrl($version_key))
                        @include('parts.link', [
                            'href' => $version_url,
                            'label' => $version_key,
                            'extra_classes' =>
                                'mr-4 group px-4 py-2 whitespace-nowrap',
                        ])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
