@extends('developer.layouts.login')

@section('contentHeader')
<title>Login | Developer Panel </title>
@stop

@section('content')
    <!-- HOME -->
    <div class="content">
        <div class="login">
            <div class="login__tbl">
                <div class="login__tblcl">
                    <div class="login__box">
                    <form method="POST" action="{{ route('developer.login') }}">
                        {{ csrf_field() }}
                            <div class="login__logo">
                                <img src="{!! asset('logo.png') !!}" alt="Developer Panel"/>
                            </div>

                            <div class="login__header">
                                <h1 class="login__title">Welcome Back!</h1>
                                <p class="login__subtitle">Please login to your account</p>
                            </div>
                            @if($errors->any())
                                <p class="login__error">
                                <i class="material-icons">error_outline</i>
                                <span>Username/Password is incorrect!</span>
                            </p>
                            @endif
                            <ul class="login__form">
                                <li>
                                    <label for="">Username/Email</label>
                                    <input type="text" name="username" class="lw_username" value="{{ old('username') }}" required>
                                </li>
                                <li>
                                    <label for="">Password</label>
                                    <input id="password" type="password" class="lw_password" name="password" required>
                                </li>
                                <li>
                                    <button type="submit" class="button login__submit" name="button">Login</button>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('contentFooter')

@stop
