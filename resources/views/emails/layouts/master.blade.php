<!doctype html>
<html lang="en">
@include('emails.includes.header')
<body>
    <div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
        <div style="margin:50px auto;width:70%;padding:20px 0">
            <div style="border-bottom:1px solid #eee;text-align: center;">
                <a href="{{ config('app.url') }}" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">{{ config('app.name') }}</a>
            </div>
            @include('emails.components.greeting',['data'=>$data])
            @yield('content')
            @include('emails.components.signature')
        </div>
    </div>
</body>
</html>
