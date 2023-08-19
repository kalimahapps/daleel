<ul class='text-sm ml-4'>
    @foreach ($data as $see_data)
        <li class='mb-2'>
            @if (str_starts_with($see_data['link'], 'http'))
                <a href="{{ $see_data['link'] }}"
                    class="text-violet-800 dark:text-violet-300 hover:underline">
                    {{ empty($see_data['description']) ? $see_data['link'] : $see_data['description'] }}
                </a>
            @else
                {{ empty($see_data['description']) ? $see_data['link'] : $see_data['description'] }}
            @endif
        </li>
    @endforeach
</ul>
