<button id="toc-toggle"
    class='border-color text-sm border px-4 py-2 bg-zinc-100 dark:bg-zinc-800 rounded mb-2 xl:hidden'>
    On this page
    <i class="bi bi-caret-down-fill text-zinc-400 text-[10px]"></i>
</button>
<ul class="border-l border-color sticky top-20 pl-4 hidden xl:block toc-content">
    <div class="max-h-[calc(100vh-6rem)] overflow-auto">
        <li class='text-xs hidden xl:block font-semibold'>On this page</li>
        <div id="toc-marker"
            class="transition-all w-px absolute -left-px bg-sky-700 h-4 hidden xl:block">
        </div>
        @foreach ($toc as $key => $data)
            <li class='text-xs'>
                @include('parts.link', [
                    'href' => "#{$key}",
                    'label' => $data['label'],
                    'extra_classes' => 'my-2 truncate w-full',
                ])
            </li>

            @if (count($data['children']) > 0)
                <ul class="pl-4">
                    @foreach ($data['children'] as $child_key => $child_data)
                        <li>
                            @include('parts.link', [
                                'href' => "#{$child_key}",
                                'label' => $child_data['label'],
                                'extra_classes' =>
                                    'my-2 text-xs truncate w-full',
                            ])
                        </li>
                    @endforeach
                </ul>
            @endif
        @endforeach
    </div>
</ul>
