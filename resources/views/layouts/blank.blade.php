<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Select Academic Period - {{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#EAF8E7] text-gray-900 dark:text-white">
    <main class="min-h-screen flex items-center justify-center p-4">
        @yield('content')
    </main>
</body>
</html>
