<!doctype html>
<html class='group/root'>

<head>
    @include('head')
</head>

<body class="group/body main-bg text-color font-inter" data-show-aside="false"
    data-show-scroll-top="false" data-show-links='false'>
    <div class="h-screen w-screen flex min-h-screen">
        <div id="backdrop"
            class="fixed inset-0 z-20 bg-black/50 cursor-pointer pointer-events-none
            opacity-0 transition-opacity
            group-data-[show-aside=true]/body:opacity-50
            group-data-[show-aside=true]/body:pointer-events-auto
            group-data-[show-links=true]/body:opacity-50
            group-data-[show-links=true]/body:pointer-events-auto
            ">
        </div>
        <aside
            class="aside-bg lg:pb-2 lg:grow lg:max-w-[30%]
            flex flex-col items-end border-r border-color h-full pl-4
            fixed top-0 left-0 z-20 transition-all duration-300 tablet:translate-x-0
            tablet:static group-data-[show-aside=true]/body:translate-x-0 -translate-x-full
            ">
            @include('aside')
        </aside>
        <div class="min-w-[70%] grow h-full overflow-auto flex flex-col transition-colors duration-300"
            id="main-wrapper">

            <!-- search, links and social bar -->
            <header
                class="
                w-[1100px] max-w-full backdrop-blur-xl sticky top-0 z-10 group-data-[show-links=true]/body:z-20
                main-bg
                ">
                @include('top-bar')
            </header>

            <!-- Main content -->
            <main
                class="w-[1100px] max-w-full px-10 tablet:px-20 pt-4 flex grow flex-col-reverse xl:flex-row">
                <div class='grow main-content flex flex-col min-w-0'>
                    @if ($config['show_breadcrumbs'] && !empty($breadcrumbs))
                        @include('breadcrumbs', [
                            'breadcrumbs' => $breadcrumbs,
                            'kind' => $kind ?? null,
                        ])
                    @endif

                    <div class="prose">
                        <!-- Display site notice if set -->
                        @php($notice = \KalimahApps\Daleel\BladeUtil::getConfig('notice'))
                        @if ($notice)
                            <div
                                class="custom-container {{ $notice['type'] }} !mb-12">
                                <div class="custom-container-content">
                                    {!! \KalimahApps\Daleel\Common::replaceTags($notice['message']) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Display page content -->
                        @yield('content')
                    </div>

                    <!-- Footer -->
                    <footer class="mt-auto">
                        @include('footer')
                    </footer>
                </div>

                <!-- Table of contents -->
                @if (!empty($toc))
                    <div class="lg:w-[180px] shrink-0" id="toc">
                        @include('toc')
                    </div>
                @endif
            </main>
        </div>
        <div class="
                fixed bottom-8 right-8 z-20 text-4xl text-color cursor-pointer
                transition-all duration-200 pointer-events-none
                text-blue-gray-500 hover:opacity-100 opacity-70
                group-data-[show-scroll-top=true]/body:pointer-events-auto
                group-data-[show-scroll-top=false]/body:opacity-0
                "
            id="scroll-to-top">
            <i class="bi bi-arrow-up-square-fill text-inherit"></i>
        </div>
    </div>
    @include('scripts')
</body>

</html>
