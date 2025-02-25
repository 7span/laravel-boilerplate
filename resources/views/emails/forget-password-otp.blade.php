@extends('emails.layouts.master')

@section('content')
<p> {{ __('email.verifyUserLine1', [
        'subject' => $data['subject'],
        'expirationTime' => config('site.otp_expiration_time_in_minutes'),
    ]) }}
</p>

<h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">
    {{ $data['otp'] }}</h2>
@endsection
