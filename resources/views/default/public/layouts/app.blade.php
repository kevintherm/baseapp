@use('\App\Models\Setting')
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ Setting::retrieve('app_name', config('app.name')) }}</title>

    <link rel="icon" href="{{ Setting::retrieve('app_favicon', storage_path: true) }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *:not(button,input) {
            transition: all 500ms;
        }
    </style>

    @yield('head')
</head>
<body class="overflow-x-hidden antialiased scroll-smooth">
    @yield('body')

    @yield('modal')

    @yield('foot')
</body>
</html>
