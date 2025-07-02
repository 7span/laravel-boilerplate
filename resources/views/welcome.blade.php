<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        .text-center {
            margin: 0;
            font-family: 'Nunito', sans-serif;
            display: flex;
            justify-content: center;
            min-height: 95vh;
            align-items: center;
        }
    </style>
</head>

<body class="antialiased">
    <div class="text-center">
        <h3>{{ config('app.name') }}</h3>
    </div>

    @if (App::environment('local') || App::environment('development'))
        <footer style="text-align: center">
            Laravel <b>v{{ Illuminate\Foundation\Application::VERSION }}</b> (PHP <b>v{{ PHP_VERSION }}</b>)
        </footer>
    @endif
</body>

</html>
