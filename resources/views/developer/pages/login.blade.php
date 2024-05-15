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

	<link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}"/>
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/developer/login.css') }}">
</head>
<body class="flex h-screen bg-black">
	<div class="w-full max-w-sm m-auto bg-white rounded p-5">
      	<header>
        	<img class="w-40 mx-auto mb-5" src="{{ asset('assets/img/logo.png') }}" />
      	</header>
    	<div class="mt-1 mb-4">
    		<h2 class="font-bold text-2xl mb-10px">Welcome Back!</h2>
  			<i>Please login to your account</i>
    	</div>
    	@if ($errors->any())
	    	<div class="bg-red-50 border border-red-400 rounded text-red-800 text-sm p-4 mb-4">
				<div>
				    <span class="font-bold">Username/Password is incorrect!</span>
				</div>
	  		</div>
        @endif
      	<form method="POST" action="{{ route('developer.login') }}" class="mt-45px">
      		@csrf
        	<div>
          		<label class="block mb-2" for="username">Username/Email</label>
          		<input class="w-92 p-2 mb-6 text-black-700 b-input outline-none bg-red-50-focus" type="text" name="username">
        	</div>
	        <div>
	          	<label class="block mb-2" for="password">Password</label>
	          	<input class="w-92 p-2 mb-6 text-black-700 b-input outline-none bg-red-50-focus" type="password" name="password">
	        </div>
	        <div>
	          	<input class="w-98 bg-red-600 bg-black-hover text-white font-bold py-2 px-4 mb-6 rounded b-none" type="submit" value="Login">
	        </div>
      	</form>
    </div>
</body>
</html>