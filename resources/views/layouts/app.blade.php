<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'TravelKu')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>

<body class="@yield('body-class')">

    @include('components.navbar')

    <main id="konten">
        @yield('content')
    </main>

    @include('components.footer')

</body>
</html>
