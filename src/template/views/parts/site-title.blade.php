<a href={{ \KalimahApps\Daleel\BladeUtil::getRootLink() }}
    class='py-4 font-medium text-lg mb-4 tablet:border-b border-color {{ $classes ?? '' }}'>
    @if (\KalimahApps\Daleel\BladeUtil::getConfig('logo'))
        <img src="/{{ \KalimahApps\Daleel\BladeUtil::getConfig('logo') }}"
            class='mr-2 max-w-[24px] h-auto' />
    @endif
    <span>
        {{ \KalimahApps\Daleel\BladeUtil::getConfig('title') }}
    </span>
</a>
