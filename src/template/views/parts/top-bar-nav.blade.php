<div class='text-sm flex flex-col tablet:flex-row'>
    @foreach ($nav as $nav_item)
        <div class="relative group mb-4 tablet:mb-0">
            <div class="inline mr-4 tablet:cursor-pointer">
                @if (!empty($nav_item['link']))
                    @include('parts.link', [
                        'href' => $nav_item['link'],
                        'label' => $nav_item['label'],
                        'extra_classes' =>
                            'inline font-bold tablet:font-normal',
                        'extra' => !empty($nav_item['items'])
                            ? '<i class="bi bi-caret-down-fill text-zinc-400 text-[10px] hidden tablet:inline"></i>'
                            : '',
                    ])
                @else
                    <span class='inline font-bold tablet:font-normal'>
                        <span>{{ $nav_item['label'] }} </span>
                        <i
                            class="bi bi-caret-down-fill text-zinc-400 text-[10px] hidden tablet:inline"></i>
                    </span>
                @endif
                @if (!empty($nav_item['items']))
                @endif
            </div>
            {{-- build popup for one level only --}}
            @if (!empty($nav_item['items']))
                <div
                    class="
                            tablet::top-full relative tablet:absolute tablet:left-1/2 tablet:-translate-x-1/2 z-10 pt-2 tablet:pointer-events-none
                            group-hover:pointer-events-auto transition-all tablet:opacity-0 group-hover:opacity-100
                            ">
                    <div
                        class=" main-bg tablet:shadow-xl rounded tablet:border border-color text-sm tablet:text-inherit">
                        @foreach ($nav_item['items'] as $sub_nav_item)
                            @include('parts.link', [
                                'href' => $sub_nav_item['link'],
                                'label' => $sub_nav_item['label'],
                                'extra_classes' =>
                                    'mr-4 group tablet:px-4 tablet:py-2 py-1 whitespace-nowrap',
                            ])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endforeach
    @include('parts.top-bar-separator')
</div>
