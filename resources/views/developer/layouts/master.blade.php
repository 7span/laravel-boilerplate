<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        Developer Panel
    </title>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    @include('developer.includes.head')

    @yield('contentHeader')
</head>

<body>
<div class="wrap">

    @yield('content')

    @include('developer.includes.footer_scripts')
</div>
@yield('contentFooter')
</body>
</html>
