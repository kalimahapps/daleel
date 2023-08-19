<div class="text-sm mb-14">
    <!-- Display flags (static, public, etc.) for the method. -->
    @if (!empty($method_data['flags']))
        @include('parts.flags', [
            'flags' => $method_data['flags'],
        ])
    @endif

    <!-- Display the method name and signature. -->
    <h3 id="{{ $method_key }}" class='mt-0 scroll-mt-20 relative group'>
        @include('parts.anchor', [
            'href' => $method_key,
        ])
        <span class='text-violet-800 dark:text-violet-300 text-base'>
            {{ $method_key }}
        </span>

        <span class="font-normal text-sm ml-1">
            <span>(</span>
            @if (!empty($method_data['params']))
                <span class="text-zinc-500 dark:text-zinc-400">
                    @foreach ($method_data['params'] as $param_key => $param_data)
                        <span>
                            @include('parts.type', [
                                'types' => $param_data['types'],
                                'output_type' => 'string',
                            ])
                        </span>
                        <span class="font-bold">
                            ${{ $param_key }}
                        </span>
                        @if (!$loop->last)
                            <span>
                                ,
                            </span>
                        @endif
                    @endforeach
                </span>
            @endif
            <span>)</span>
            <span class="text-zinc-500">
                <span>:</span>
                @if (!isset($method_data['docblock']['tags']['return']))
                    <span class="text-zinc-500 dark:text-zinc-400">
                        void
                    </span>
                @else
                    @include('parts.type', [
                        'types' =>
                            $method_data['docblock']['tags']['return'][
                                'types'
                            ],
                    ])
                @endif
            </span>
        </span>
    </h3>
    @if (!empty($method_data['docblock']['summary']))
        <p>{!! $method_data['docblock']['summary'] !!}</p>
    @endif

    <!-- Display the method paramters from docblock. -->
    @if (!empty($method_data['docblock']['tags']['params']))
        <h4 class="text-zinc-600 font-normal">Parameters</h4>
        <ul class='ml-4'>
            @foreach ($method_data['docblock']['tags']['params'] as $param_key => $param_data)
                <li class='mb-2'>
                    <span class='font-medium'>
                        ${{ $param_key }}
                    </span>
                    <span class="text-rose-500 dark:text-rose-400">
                        @include('parts.type', [
                            'types' => $param_data['types'],
                            'output_type' => 'string',
                        ])
                    </span>

                    <div>{!! $param_data['description'] !!}</div>
                </li>
            @endforeach
        </ul>
    @endif

    <!-- Display see links from docblock. -->
    @if (!empty($method_data['docblock']['tags']['see']))
        <h4 class="text-zinc-600 font-normal">See</h4>
        @include('parts.see', [
            'data' => $method_data['docblock']['tags']['see'],
        ])
    @endif
</div>
