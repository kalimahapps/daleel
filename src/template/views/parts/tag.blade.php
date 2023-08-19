 @php
     $style = $style ?? 'info';
     $colors = 'bg-zinc-200 dark:bg-zinc-700';
     
     if ($style === 'warning') {
         $colors = 'bg-amber-700 text-white';
     }
 @endphp
 <span class="{{ $colors }} px-2 py-1 rounded mr-2 {{ $classes ?? '' }}"
     data-style="{{ $style }}">
     {{ $label }}
 </span>
