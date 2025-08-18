@extends('emails.layouts.master')

@section('content')
    <p>{{ __('email.forgot_password.line1') }}</p>

    <p>{{ __('email.forgot_password.line2', [
        'otp' => $otp,
        'valid_minute' => config('site.otp.expiration_time_in_minutes'),
    ]) }}
    </p>

    <p>{{ __('email.forgot_password.footer') }}</p>
@endsection
