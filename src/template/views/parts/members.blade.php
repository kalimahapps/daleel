{{--
This is a generic template for displaying object members (constants, properties)
 --}}
@foreach ($members as $member_key => $member_data)
    <div class='mb-14'>
        <!-- Display flags (static, public, etc.). -->
        @include('parts.flags', [
            'flags' => $member_data['flags'],
        ])

        <h3 id="{{ $member_key }}" class="scroll-mt-20 relative group mt-0">
            @include('parts.anchor', [
                'href' => $member_key,
            ])
            <span class='{{ $color }} text-base'>
                @if ($type === 'property')
                    ${{ $member_key }}
                @else
                    {{ $member_key }}
                @endif
            </span>

            @if (!empty($member_data['docblock']['tags']['var']['types']))
                <span
                    class="text-rose-500 dark:text-rose-400 text-sm font-normal">
                    @include('parts.type', [
                        'types' =>
                            $member_data['docblock']['tags']['var'][
                                'types'
                            ],
                    ])
                </span>
            @endif
        </h3>

        {{-- Display description if available. --}}
        @if (!empty($member_data['docblock']['description']))
            @foreach ($member_data['docblock']['description'] as $description)
                <p class="text-sm">
                    {{ $description }}
                </p>
            @endforeach
        @endif

        @if ($member_data['value'])
            <div class="mt-2 text-sm">
                Default: {{ $member_data['value'] }}
            </div>
        @endif
    </div>
@endforeach
