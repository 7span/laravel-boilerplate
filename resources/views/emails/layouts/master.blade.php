<!doctype html>
<html lang="en">
@include('emails.includes.header')
<body>
    <div class="wrapper">
        <div class="content">
            <div class="header">
                <img src="https://laravel.com/img/notification-logo.png" alt="Laravel Logo" class="logo">
            </div>

            <!-- Email Body -->
            <div class="body">
                <div class="inner-body">
                    <div class="content-cell">
                        @include('emails.components.greeting',['data'=>$data])
                        @yield('content')
                        @include('emails.components.signature')
                    </div>
                </div>
            </div>

            <div class="footer">
                <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
