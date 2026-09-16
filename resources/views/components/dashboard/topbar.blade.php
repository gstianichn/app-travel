<header class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-6">

    {{-- Page title --}}
    <div>
        <h1 class="text-lg font-semibold text-slate-800">
            @yield('title', 'Dashboard')
        </h1>
    </div>

    {{-- User --}}
    @auth
        <div class="flex items-center gap-3">

            <div class="hidden text-right sm:block">
                <p class="text-sm font-semibold text-slate-800">
                    {{ auth()->user()->name }}
                </p>

                <p class="text-xs text-slate-500">
                    {{ str_replace('_', ' ', ucfirst(auth()->user()->role)) }}
                </p>
            </div>

            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-600">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

        </div>
    @endauth

</header>