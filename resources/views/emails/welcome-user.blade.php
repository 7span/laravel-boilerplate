@extends('emails.layouts.master')

@section('content')
    <h2>{{ __('email.welcome_user.greeting', ['app_name' => config('app.name')]) }}</h2>

    <p>{{ __('email.welcome_user.content') }}</p>

    <p>{{ __('email.welcome_user.footer') }}</p>

    <p><a href="{{ config('site.front_website_url') }}" target="_blank">{{ __('email.welcome_user.action') }}</a></p>
@endsection
