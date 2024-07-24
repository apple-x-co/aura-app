{{ extends ('layout/Admin/base') }}

{{ setBlock ('body') }}
<header class="sticky top-0 z-40 w-full backdrop-blur flex-none transition-colors duration-500 lg:z-50 lg:border-b lg:border-slate-900/10 bg-white/70 supports-backdrop-blur:bg-white/60">
    {{ setBlock ('body_header') }}
    <div class="max-w-7xl mx-auto">
        <div class="py-4 border-b border-slate-900/10 px-4 lg:px-8 lg:border-0">
            <div class="relative flex items-center">
                <a class="mr-3 flex-none overflow-hidden lg:w-auto font-sans font-black text-lg bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500" href="/admin/index">
                    ADMINISTRATOR
                </a>
                <div class="relative hidden lg:flex items-center ml-auto">
                    <div class="flex items-center border-l border-slate-200 ml-6 pl-6 dark:border-slate-800">
                        <form method="post" action="/admin/logout">
                            <button>
                                {{= render('partials/Admin/InlineIcon', ['name' => 'arrow-left-on-rectangle']) }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{= getBlock () ~}}
</header>
<div class="max-w-7xl mx-auto px-0 lg:px-8 pt-5 flex flex-row grow">
    {{ $isNavVisible = isset($navVisible) ? $navVisible : true }}
    {{ if ($isNavVisible): }}
    <div class="hidden lg:block lg:w-72 lg:overflow-y-auto">
        <nav class="block leading-6 relative">
            {{ setBlock ('body_nav') }}
            {{ $viewName = $this->getView() }}
            <p>
                <a class="text-sm font-bold tracking-wider {{ if ($view == 'Page/Admin/Index'): }}text-lime-500{{ endif }}" href="/admin/index">HOME</a>
            </p>
            {{= getBlock () ~}}
        </nav>
    </div>
    {{ endif }}
    <div class="block lg:flex lg:flex-col lg:grow w-full">
        <div class="mx-5 lg:mx-0 lg:grow whitespace-normal break-words">
            <main>{{ setBlock ('body_content') }}{{= getBlock () ~}}</main>
        </div>
        <div class="mx-5 lg:mx-0 my-10">
            <footer>{{ setBlock ('body_footer') }}<p class="font-sans font-thin text-sm">Copyright &copy; 2023 apple-x-co.</p>{{= getBlock () ~}}</footer>
        </div>
    </div>
</div>
{{ endBlock () }}
