@extends('developer.layouts.master')

@section('contentHeader')
<title>Dashboard | Developer Panel </title>
@stop

@section('content')
<div class="content">
    <div class="container">
        <div class="page__row">
            <div class="page__header clearfix">
                <div class="page__title">
                    <h1 class="page__title-text">Dashboard</h1>
                </div>
            </div>
        </div>

        <div class="page__row">
            <div class="row" >
                <div class="col-xs-12 col-sm-6 col-md-3" align="center">
                    <a class="card--colorful card--purple" target="_blank" href="{{ url('/telescope') }}" style="text-decoration: none;">
                        <h3 class="card__title">Telescope</h3>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3" align="center">
                    <a class="card--colorful card--teal" target="_blank" href="{{ url('/log-viewer') }}" style="text-decoration: none;">
                        <h3 class="card__title">Log Viewer</h3>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3" align="center">
                    <a class="card--colorful card--blue" target="_blank" href="{{ url('/graphql-playground') }}" style="text-decoration: none;">
                        <h3 class="card__title">GraphQL</h3>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('contentFooter')

@stop
