  <span data-expanded='{{ $is_expanded ?? 'false' }}'
      data-target='{{ $target }}'
      class='toggle group leading-none
                mr-2 grid place-content-center cursor-pointer
                text-xl opacity-70 hover:opacity-90 transition-opacity
                rounded hover:bg-zinc-200 dark:hover:bg-zinc-700
                '>
      <span class="group-data-[expanded=true]:hidden">
          <i class="bi bi-plus"></i>
      </span>
      <span class="group-data-[expanded=false]:hidden">
          <i class="bi bi-dash"></i>
      </span>
  </span>
