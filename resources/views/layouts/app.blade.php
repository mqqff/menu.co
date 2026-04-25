<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>@yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans flex flex-col">
    @include('partials.navbar')

    <div class="flex-1">
        @yield('content')
    </div>

    @include('partials.footer')

    <script src="https://cdn.tailwindcss.com"></script>
    @stack('scripts')
</body>
</html>
