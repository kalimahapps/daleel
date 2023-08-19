 <div class='text-[0.6rem] opacity-80'>
     @foreach ($flags as $flag_key => $flag_value)
         @if ($flag_value)
             @include('parts.tag', [
                 'label' =>
                     $flag_key === 'visibility' ? $flag_value : $flag_key,
                 'style' =>
                     $flag_key === 'deprecated' ? 'warning' : 'info',
             ])
         @endif
     @endforeach
 </div>
