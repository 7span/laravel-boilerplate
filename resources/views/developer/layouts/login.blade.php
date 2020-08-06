<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title>
            Developer Panel
		</title>

        @include('developer.includes.head')

        @section('contentHeader')
        @stop
	</head>

	<body>
        <div class="wrap">
            @yield('content')
        </div>

        @include('developer.includes.footer_scripts')
        @yield('contentFooter')
	</body>
</html>
