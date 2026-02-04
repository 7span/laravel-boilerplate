<!DOCTYPE html>
<html lang="en">
<head>
     <!-- META -->
	<meta charset="utf-8"/>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
    <title>Login - Developer Panel</title>
	<link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/developer/login.css') }}">
</head>
<body>
    <div class="container">
        <div class="login-wrapper">
            <!-- Logo -->
            <div class="logo-container">
                <div class="logo-wrapper">
                   <img width="140" src="{{ asset('assets/img/logo.png') }}" alt="Logo">
                </div>
            </div>
            
            <!-- Login Form -->
            <div class="login-card">
                <form id="loginForm" action="{{ route('developer.login') }}" method="POST">
					@csrf
					<h3 class="welcome-text">Welcome Back !</h3>
                    <div class="form-group">
                        <label for="email" class="form-label">Username</label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-86.4 32C61.7 288 0 349.7 0 425.6C0 448.4 16.6 464 39.4 464H408.6c22.8 0 39.4-15.6 39.4-38.4C448 349.7 386.3 288 310.4 288H137.6z"/>
                                </svg>
                            </div>
                            <input type="text" name="username" id="username" class="form-input" placeholder="Enter your username">
                        </div>
						@error('username')
							<div class="error-message">
								<p>{{ $message }}</p>
							</div>
						@enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <path d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z"/>
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" class="form-input" placeholder="Enter your password">
                            <button type="button" id="togglePassword" class="toggle-password">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/>
                                </svg>
                            </button>
                        </div>
						@error('password')
							<div class="error-message">
								<p>{{ $message }}</p>
							</div>
						@enderror
                    </div>
                    
                    <button type="submit" class="submit-button">
                        Log in
                    </button>
                </form>
            </div>    
        </div>
    </div>
    
    <script>
        const pwd = document.getElementById('password'), eye = document.getElementById('eyeIcon');
        document.getElementById('togglePassword').onclick = () => {
            const show = pwd.type === 'password';
            pwd.type = show ? 'text' : 'password';
            eye.innerHTML = show
                ? '<path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L525.6 386.7c39.6-40.6 66.4-86.1 79.9-118.4c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C465.5 68.8 400.8 32 320 32c-68.2 0-125 26.3-169.3 60.8L38.8 5.1zM223.1 149.5C248.6 126.2 282.7 112 320 112c79.5 0 144 64.5 144 144c0 24.9-6.3 48.3-17.4 68.7L408 294.5c8.4-19.3 10.6-41.4 4.8-63.3c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3c0 10.2-2.4 19.8-6.6 28.3l-90.3-70.8zM373 389.9c-16.4 6.5-34.3 10.1-53 10.1c-79.5 0-144-64.5-144-144c0-6.9 .5-13.6 1.4-20.2L83.1 161.5C60.3 191.2 44 220.8 34.5 243.7c-3.3 7.9-3.3 16.7 0 24.6c14.9 35.7 46.2 87.7 93 131.1C174.5 443.2 239.2 480 320 480c47.8 0 89.9-12.9 126.2-32.5L373 389.9z"/>'
                : '<path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/>';
        };
    </script>
</body>
</html>