<!DOCTYPE html>
<html>
<head>
	<title>Developer Panel</title>

    <!-- META -->
	<meta charset="utf-8"/>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">

	<link rel="shortcut icon" href="{{ asset('assets/img/favicon.png')}}"/>
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/developer/dashboard.css')}}">
</head>
<body class="d-flex max-h-full bg-white justify-center">
	<div class="justify-center">
	    <header class="bg-white shadow">
	        <div class="max-w-7xl mx-auto px-2 lg-divide-gray-200">
	            <div class="relative h-20 d-flex justify-between">
	                <div class="relative z-0 flex-1 px-2 d-flex items-center justify-center sm-absolute sm-inset-0">
                        <div class="relative">
                            <a href="{{ config('app.url') }}">
                            	<img class="h-12" src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}">
                            </a>
                        </div>
	                </div>
	            </div>
	        </div>
	    </header>
	    <div class="d-flex justify-center pt-5">
	        <div class="log-viewer p-5">
	            <a href="{{ url('developer/log-viewer') }}" target="_blank">
	                <div class="block">
	                    <div class="p-6">
	                        <h5>Log Viewer</h5>
	                        <p>
	                            Log Viewer supports multiple logs! You can see single, daily, and horizon logs.
	                        </p>
	                    </div>
	                </div>
	            </a>
	        </div>
	        <div class="telescope p-5">
	            <a href="{{ url('developer/telescope') }}" target="_blank">
	                <div class="block">
	                    <div class="p-6">
	                        <h5>Telescope</h5>
	                        <p>
	                            Telescope provides insight into the requests coming into your application, exceptions, database queries, queued jobs, scheduled tasks and more.
	                        </p>
	                    </div>
	                </div>
	            </a>
	        </div>
	    </div>
	</div>
</body>
</html>