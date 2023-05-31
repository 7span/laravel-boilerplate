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
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex max-h-full bg-white justify-center">
	<div class="justify-center">
	    <header class="bg-white shadow">
	        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:divide-y lg:divide-gray-200 lg:px-8">
	            <div class="relative h-20 flex justify-between">
	                <div class="relative z-0 flex-1 px-2 flex items-center justify-center sm:absolute sm:inset-0">
	                    <div class="w-full max-w-xs">
	                        <div class="relative">
	                            <a href="{{ config('app.url') }}"><img class="h-12 my-0 mx-auto" src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}"></a>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </header>
	    <div class="flex justify-center pt-5">
	        <div class="p-5">
	            <a href="{{ url('developer/log-viewer') }}" target="_blank">
	                <div class="block rounded-lg shadow-lg max-w-sm text-center bg-blue-700 h-full h-200">
	                    <div class="p-6">
	                        <h5 class="text-white text-xl font-medium mb-2">Log Viewer</h5>
	                        <p class="text-white text-base mb-4">
	                            Log Viewer supports multiple logs! You can see single, daily, and horizon logs.
	                        </p>
	                    </div>
	                </div>
	            </a>
	        </div>
	        <div class="p-5">
	            <a href="{{ url('developer/telescope') }}" target="_blank">
	                <div class="block rounded-lg shadow-lg bg-purple-700 max-w-sm text-center h-full h-200">
	                    <div class="p-6">
	                        <h5 class="text-white text-xl font-medium mb-2">Telescope</h5>
	                        <p class="text-white text-base mb-4">
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