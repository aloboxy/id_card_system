@extends('layouts.auth')

@section('content')
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <div class="logo-icon mb-4 flex justify-center">
                    @if(isset($systemLogo) && $systemLogo)
                        <img src="{{ asset('storage/' . $systemLogo) }}" alt="Logo" class="h-16 w-16 rounded-full object-cover">
                    @else
                        <i class="fas fa-id-card text-4xl text-white"></i>
                    @endif
                </div>
                <h1 class="welcome-text">{{ $systemName }}</h1>
                <p class="subtitle-text">Please enter your details.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <input id="email" type="email" class="form-control" style="color: white;" name="email" 
                           value="{{ old('email') }}" required autofocus 
                           placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <input id="password" type="password" class="form-control" style="color: white;" name="password" 
                           required placeholder="••••••••">
                </div>

                <div class="options-row">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember for 30 days</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password</a>
                    <a href="{{ route('register') }}" class="forgot-link">Register</a>

                </div>

                <button type="submit" class="btn-signin">
                    Sign in
                </button>
            </form>
        </div>
    </div>
@endsection

