@php
    $bootstrap_icons = [
        'github' => 'bi-github',
        'gitlab' => 'bi-gitlab',
        'bitbucket' => 'bi-bitbucket',
        'facebook' => 'bi-facebook',
        'twitter' => 'bi-twitter',
        'linkedin' => 'bi-linkedin',
        'youtube' => 'bi-youtube',
        'instagram' => 'bi-instagram',
        'discord' => 'bi-discord',
    ];
@endphp
@include('parts.top-bar-separator')
<div
    class='flex justify-center mt-2 pt-4 border-t border-color tablet:mt-0 tablet:pt-0 tablet:border-t-0 group-data-[page=index]/body:border-t-0 group-data-[page=index]/body:mt-0 group-data-[page=index]/body:pt-0'>
    @foreach ($social_links as $social_key => $social_data)
        @if (isset($bootstrap_icons[$social_key]))
            <a href="{{ $social_data['link'] }}" target="_blank"
                class="text-zinc-400 hover:text-zinc-500 dark:hover:text-zinc-200 mr-3 transition-colors">
                <i class="{{ $bootstrap_icons[$social_key] }}"></i>
            </a>
        @endif
    @endforeach
</div>
