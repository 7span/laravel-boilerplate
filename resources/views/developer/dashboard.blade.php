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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/developer/dashboard.css') }}">
</head>

<body>
    <header class="header">
        <div>
            <a href="{{ config('app.url') }}">
                <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}" height="40">
            </a>
        </div>
    </header>

    <div class="dashboard-container">
        <a href="{{ url('developer/log-viewer') }}" target="_blank" class="card">
            <i class="fas fa-file-alt card-icon" style="color: #3b82f6;"></i>
            <h5>Log Viewer</h5>
            <p>View and analyze different types of logs with ease.</p>
        </a>
        <a href="{{ url('developer/telescope') }}" target="_blank" class="card">
            <i class="fas fa-binoculars card-icon" style="color: #10b981;"></i>
            <h5>Telescope</h5>
            <p>Gain insights into requests, exceptions, and database queries.</p>
        </a>
        <a href="{{ url('developer/docs/api') }}" target="_blank" class="card">
            <i class="fas fa-book card-icon" style="color: #f59e0b;"></i>
            <h5>Swagger API Document</h5>
            <p>Easily document and streamline RESTful API development.</p>
        </a>
        <a href="{{ url('developer/horizon') }}" target="_blank" class="card">
            <i class="fas fa-chart-line card-icon" style="color: #9333ea;"></i>
            <h5>Horizon</h5>
            <p>Monitor queue system metrics like job throughput and failures.</p>
        </a>
        <a href="{{ url('developer/pulse') }}" target="_blank" class="card">
            <i class="fas fa-heartbeat card-icon" style="color: #ef4444;"></i>
            <h5>Pulse</h5>
            <p>Track application performance, slow jobs, and active users.</p>
        </a>
    </div>
</body>

</html>
