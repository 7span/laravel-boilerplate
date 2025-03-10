<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Developer Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/developer/dashboard.css') }}">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="{{ config('app.url') }}">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}">
                </a>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- Log Viewer Card -->
        <a href="{{ url('developer/log-viewer') }}" target="_blank" class="card">
            <div class="card-content">
                <i class="fas fa-file-alt card-icon"></i>
                <h5>Log Viewer</h5>
                <p>Log Viewer supports multiple logs! You can see single, daily, and horizon logs.</p>
            </div>
        </a>

        <!-- Telescope Card -->
        <a href="{{ url('developer/telescope') }}" target="_blank" class="card">
            <div class="card-content">
                <i class="fas fa-binoculars card-icon"></i>
                <h5>Telescope</h5>
                <p>Telescope provides insight into requests, exceptions, database queries, queued jobs, and more.</p>
            </div>
        </a>

        <!-- Swagger API Document Card -->
        <a href="{{ url('developer/docs/api') }}" target="_blank" class="card">
            <div class="card-content">
                <i class="fas fa-book card-icon"></i>
                <h5>Swagger API Document</h5>
                <p>Swagger streamlines RESTful web service development with easy-to-use documentation tools.</p>
            </div>
        </a>

        <!-- Horizon Card -->
        <a href="{{ url('developer/horizon') }}" target="_blank" class="card">
            <div class="card-content">
                <i class="fas fa-chart-line card-icon"></i>
                <h5>Horizon</h5>
                <p>Monitor key metrics of your queue system, such as job throughput, runtime, and failures.</p>
            </div>
        </a>

        <!-- Pulse Card -->
        <a href="{{ url('developer/pulse') }}" target="_blank" class="card">
            <div class="card-content">
                <i class="fas fa-heartbeat card-icon"></i>
                <h5>Pulse</h5>
                <p>Track application performance, slow jobs, active users, and more with Laravel Pulse.</p>
            </div>
        </a>
    </div>
</body>

</html>
