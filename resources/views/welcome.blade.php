<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else

    @endif
</head>

<body>
    <!-- Background Container with Opacity -->
    <div style="position: absolute; width: 100%; height: 100vh; 
    background: url('{{ asset('assets/resturent.jpg') }}') no-repeat center center fixed; 
    background-size: cover; opacity: 0.6; z-index: -1;"></div>

    <!-- Main Content -->
    <div style="position: relative; width: 100%; height: 100vh; z-index: 1;">
        <!-- Header Section -->
        <header style="width: 100%; padding: 10px 0; display: flex; justify-content: flex-end; position: relative;">
            <div style="text-align: center; flex-grow: 1;">
                <!-- Logo or content goes here -->
            </div>
            @if (Route::has('login'))
            <nav style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('login') }}"
                    style="padding: 8px 16px; color: black; background-color: #e8e5e0; border: 1px solid transparent; text-decoration: none; transition: color 0.3s; border-radius: 4px; font-weight: bold;">
                    Log in
                </a>

                @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    style="padding: 8px 16px; color: black; background-color: #e8e5e0; border: 1px solid transparent; text-decoration: none; transition: color 0.3s; border-radius: 4px; font-weight: bold;">
                    Register
                </a>
                @endif
            </nav>
            @endif
        </header>


        <!-- Body Section -->
        <main
            style="margin-top: 100px; display: flex; justify-content: center; align-items: center; height: calc(100vh - 100px); text-align: center; color: black;">
            <div style="text-align: center;">
                <h1 style="font-size: 5rem; font-weight: bold; color: Black; margin: 0;">
                    Restaurant Management System
                </h1>
            </div>
        </main>
    </div>
</body>


</html>