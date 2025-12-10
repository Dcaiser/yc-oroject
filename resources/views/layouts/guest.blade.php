<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Al-Ruhamaa' | Inventory System</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/logo/icon-green.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/logo/icon-green.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/logo/icon-green.png') }}">
        <link rel="shortcut icon" href="{{ asset('assets/logo/icon-green.png') }}">
        
        <!-- Web App Manifest -->
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="theme-color" content="#047857">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{ $slot }}
    </body>
</html>