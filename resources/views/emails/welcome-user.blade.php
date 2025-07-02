@extends('emails.layouts.master')

@section('content')
    <h2>{{ __('email.welcomeUser.greeting', ['app_name' => config('app.name')]) }}</h2>

    <p>{{ __('email.welcomeUser.content') }}</p>

    <p>{{ __('email.welcomeUser.footer') }}</p>

    <p><a href="{{ config('site.front_website_url') }}" target="_blank">{{ __('email.welcomeUser.action') }}</a></p>
@endsection
