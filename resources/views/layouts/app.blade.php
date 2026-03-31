<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    @vite('resources/css/app.css', 'resources/js/app.js')
</head>
<body class="min-h-screen font-sans">
    <div class="container">
        @yield('content')
    </div>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
