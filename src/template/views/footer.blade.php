@php($footer = \KalimahApps\Daleel\BladeUtil::getConfig('footer'))

<div class='text-sm text-center mt-16 prose'>
    @foreach ($footer as $footerItem)
        <div>{!! $footerItem !!}</div>
    @endforeach
</div>
