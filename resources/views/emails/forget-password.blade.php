@extends('emails.layouts.master')

@section('content')
<p>{{ __('email.forgetPasswordLinkEmailLine1') }}</p>

<p>{{ __('email.forgetPasswordLinkEmailLine2') }}</p>

<div class="action">
    <a href="{{ $url }}" target="_blank" class="button button-dark">
        {{ __('email.link') }}
    </a>
</div>
@endsection
