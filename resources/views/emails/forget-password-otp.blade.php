@extends('emails.layouts.master')

@section('content')
<p> {{ __('email.verifyUserLine1', [
        'subject' => $data['subject'],
        'expirationTime' => config('site.otp_expiration_time_in_minutes'),
    ]) }}
</p>

<p class="email-button">{{ $data['otp'] }}</p>
@endsection
