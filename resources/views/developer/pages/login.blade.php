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
<body class="flex h-screen bg-black">
	<div class="w-full max-w-sm m-auto bg-white rounded p-5">   
      	<header>
        	<img class="w-40 mx-auto mb-5" src="{{ asset('assets/img/logo.png')}}" />
      	</header>   
    	<div class="mt-1 mb-4">
    		<h2 class="font-bold text-2xl text-black-500">Welcome Back!</h2>
  			<i class="text-xs text-black-500">Please login to your account</i>
    	</div>
      	<form method="POST" action="{{ route('developer.login') }}">
      		@csrf
        	<div>
          		<label class="block mb-2 text-gray-300" for="username">Username/Email</label>
          		<input class="w-full p-2 mb-6 text-black-700 border-b-2 border-zinc-400 outline-none focus:bg-red-50" type="text" name="username">
        	</div>
	        <div>
	          	<label class="block mb-2 text-gray-300" for="password">Password</label>
	          	<input class="w-full p-2 mb-6 text-black-700 border-b-2 border-zinc-400 outline-none focus:bg-red-50" type="password" name="password">
	        </div>
	        <div>          
	          	<input class="w-full bg-red-600 hover:bg-black text-white font-bold py-2 px-4 mb-6 rounded" type="submit" value="Login">
	        </div>       
      	</form>  
    </div>
</body>
</html>