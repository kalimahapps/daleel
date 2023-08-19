@php($is_active = KalimahApps\Daleel\BladeUtil::isActiveRoute($active_route, $href, true))

<a class="
      block link-color
      border-transparent border-l -ml-px
      transition-all duration-100
      {{ $is_active ? 'font-semibold link-color-active' : '' }}
      {{ $extra_classes ?? '' }}"
    href="{{ $href }}" title="{{ $label }}"
    target="{{ $target }}">{{ $label }}
    {!! $extra ?? '' !!}
</a>
