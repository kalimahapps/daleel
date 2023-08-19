@php($social_links = \KalimahApps\Daleel\BladeUtil::getConfig('social_links'))
<!doctype html>
<html class='group/root'>

<head>
    @include('head')
</head>

</html>

<body data-page="index" class="group/body">
    <div
        class="main-bg text-color min-h-screen font-inter dark:text-zinc-100 flex flex-col items-center">
        <div class="w-[1100px] max-w-full grow">
            <div class='flex items-center justify-end py-5'>
                @include('parts.dark-toggle', [
                    'classes' => 'block',
                ])
                @if ($social_links !== false)
                    @include('parts.top-bar-social', [
                        'social_links' => $social_links,
                    ])
                @endif
            </div>
            <div class="flex flex-col grow items-center justify-center">
                <div class="pb-10 pt-20 flex flex-col items-center">
                    @if (\KalimahApps\Daleel\BladeUtil::getConfig('logo'))
                        <img src="/{{ \KalimahApps\Daleel\BladeUtil::getConfig('logo') }}"
                            class='max-w-[64px] tablet:max-w-[128px] h-auto' />
                    @endif
                    <h1 class="text-3xl tablet:text-6xl font-bold pt-6 pb-3">
                        {{ $data['title'] }}
                    </h1>
                    <h2
                        class="text-xl tablet:text-2xl font-medium pb-6 text-center">
                        {{ $data['subtitle'] }}
                    </h2>
                </div>
                <div class="flex justify-center">
                    @if ($data['buttons'])
                        @foreach ($data['buttons'] as $index => $button)
                            @if ($index === 0)
                                <a type="button"
                                    href="{{ \KalimahApps\Daleel\Common::replaceTags($button['link']) }}"
                                    class="mr-6 text-white bg-cyan-700 hover:bg-cyan-800 transition-colors duration-300 font-medium rounded-3xl px-6 py-2 mb-2 dark:bg-cyan-600 dark:hover:bg-cyan-700 dark:text-zinc-300">
                                    {{ $button['label'] }}
                                </a>
                            @else
                                <a type="button" href="{{ $button['link'] }}"
                                    class="bg-zinc-200 hover:bg-zinc-300 transition-colors duration-300 font-medium rounded-3xl px-5 py-2.5 mr-2 mb-2 dark:bg-zinc-600 dark:hover:bg-zinc-700 dark:text-zinc-300">
                                    {{ $button['label'] }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <!-- Footer -->
        <footer class="mt-auto pb-4">
            @include('footer')
        </footer>
        @include('scripts')
    </div>
</body>
