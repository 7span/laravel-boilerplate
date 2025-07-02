<!doctype html>
<html lang="en">
@include('emails.includes.header')

<body>
    <div class="wrapper">
        <div class="content">
            <div class="header">
                <img src="{{ asset('assets/img/logo.png') }}" alt="{{ config('app.name') }}" class="logo">
            </div>

            <div class="body">
                <div class="inner-body">
                    <div class="content-cell">
                        <p>
                            {{ __('email.hello') }} {{ $name ?? null }},
                        </p>

                        @yield('content')

                        <p>
                            {{ __('email.regards') }}<br />{{ config('app.name') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="footer">
                <p>
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>

</html>
