<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('assets/css/developer/auth.css') }}">
</head>

<body>
    <div class="container">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="logo">
        <h2>Welcome Back</h2>

        @if ($errors->any())
            <div class="alert">
                <strong>Error:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('developer.authenticate') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="username">Username/Email:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username or email"
                    required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>

</html>
