@extends('emails.layouts.master')

@section('content')
<p>
    {{ __('email.verifyUserLine1', [
        'subject' => $data['subject'],
        'expirationTime' => config('site.otp_expiration_time_in_minutes'),
    ]) }}
</p>

<div class="action">
    <span class="button button-dark">{{ $data['otp'] }}</span>
</div>
@endsection
