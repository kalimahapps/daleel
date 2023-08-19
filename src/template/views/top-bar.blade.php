@php
    $social_links = \KalimahApps\Daleel\BladeUtil::getConfig('social_links');
@endphp
<div class="w-full px-6 py-3 tablet:py-0 flex items-center">
    <div class="text-zinc-600 text-2xl cursor-pointer tablet:hidden mr-4"
        id="sidebar-toggle">
        <i class="bi bi-list"></i>
    </div>
    @include('parts.site-title', [
        'classes' => 'tablet:hidden flex text-sm !py-1 !mb-0',
    ])
    <div class='mr-auto'>
        @if (\KalimahApps\Daleel\BladeUtil::isSearchEnabled())
            <div id="search">
                <i class="bi bi-search text-xs"></i> <span
                    class='pl-2'>search</span>
            </div>
        @endif
    </div>
    @include('parts.dark-toggle', [
        'classes' => 'block tablet:hidden mr-4',
    ])
    <!-- Responsive popup -->
    <div>
        <div class="text-xl relative cursor-pointer tablet:hidden group-data-[show-links=true]/body:z-30"
            id="links-toggle">
            <i class="bi bi-three-dots"></i>
        </div>
        <div
            class='
            fixed tablet:relative flex-col tablet:flex-row
            tablet:bg-transparent tablet:dark:bg-transparent md:w-auto w-full rounded p-5 md:right-5
            left-0 md:left-auto shadow-lg tablet:shadow-none border tablet:border-none border-color top-full
            hidden tablet:flex group-data-[show-links=true]/body:flex
            group-data-[show-links=true]/body:z-30
            '>
            @if ($navbar)
                @include('parts.top-bar-nav', ['nav' => $navbar])
            @endif
            @include('parts.dark-toggle', [
                'classes' => 'hidden tablet:block',
            ])
            @if ($social_links !== false)
                @include('parts.top-bar-social', [
                    'social_links' => $social_links,
                ])
            @endif
        </div>
    </div>

</div>
