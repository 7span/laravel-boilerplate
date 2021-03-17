@component('mail::message')
{{ __('email.hello') }} {{ $data['name'] }},

{{ __('email.forgetPasswordEmailLine1',['otp' => $data['otp']]) }}
<br>
<br>
{{ __('email.forgetPasswordEmailLine2') }}

{{ __('email.thanks') }},<br>
@endcomponent