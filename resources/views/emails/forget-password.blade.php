@component('mail::message')

{{ __('email.hello') }} {{ $data['name'] }},<br>

{{ __('email.forgetPasswordEmailLine1') }}

<div class="otp">{{$data['otp']}}</div>

{{ __('email.forgetPasswordEmailLine2') }}<br>

{{ __('email.thanks') }},<br>
{{ config('app.name') }}

@endcomponent