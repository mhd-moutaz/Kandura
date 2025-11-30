<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="h2GDEKQxK1975Uy1UmS2cJ0TWzxbsXF0EZK1idNL">
    <title>Login - My Online Store</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/login/style.css') }}" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">
                <i class="fas fa-store"></i>
                <h1>KANDURA STORE</h1>
            </div>

            <div class="alert error" id="errorAlert">
                <i class="fas fa-exclamation-circle"></i>
                <span id="errorMessage">Username or password is incorrect</span>
            </div>
            @if($errors->any())
                <div class="alert error" id="errorAlert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="errorMessage">{{ $errors->first() }}</span>
                </div>
            @elseif(session('error'))
                <div class="alert error" id="errorAlert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="errorMessage">{{ session('error') }}</span>
                </div>
            @endif
            <form action="{{ route('login_action') }}" method="POST" class="login-form" id="loginForm">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password"
                            required>
                    </div>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>


        </div>
    </div>
</body>

</html>
