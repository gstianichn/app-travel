<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Dashboard') - Travel
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        @include('components.dashboard.sidebar')

        <div class="flex min-w-0 flex-1 flex-col">

            {{-- Topbar --}}
            @include('components.dashboard.topbar')

            {{-- Main Content --}}
            <main class="flex-1 p-6">
                @yield('content')
            </main>

        </div>

    </div>

</body>

</html>