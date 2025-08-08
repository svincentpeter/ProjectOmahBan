<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

    <title>Login | {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}">

    <!-- CoreUI CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <style>
      body {
        background: #f5f7fa;
      }
      .login-card {
        border: none;
        border-radius: .5rem;
      }
      .login-card .card-body {
        padding: 2rem;
      }
      .login-title {
        font-weight: 600;
        margin-bottom: .5rem;
      }
    </style>
</head>

<body class="c-app d-flex align-items-center justify-content-center min-vh-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-8 col-md-6 col-lg-4">

        {{-- Logo --}}
        <div class="text-center mb-4">
          <img src="{{ asset('images/Logo.png') }}" alt="Logo" width="180">
        </div>

        {{-- Alert jika ada --}}
        @if(Session::has('account_deactivated'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ Session::get('account_deactivated') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        {{-- Card Login --}}
        <div class="card shadow-sm login-card">
          <div class="card-body">
            <form id="login" method="post" action="{{ url('/login') }}">
              @csrf

              <h2 class="login-title text-center">Sign In</h2>
              <p class="text-center text-muted mb-4">Masuk ke akun Anda</p>

              {{-- Email --}}
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                  <input id="email" type="email"
                         class="form-control @error('email') is-invalid @enderror"
                         name="email" value="{{ old('email') }}"
                         placeholder="you@example.com" required autofocus>
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              {{-- Password --}}
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input id="password" type="password"
                         class="form-control @error('password') is-invalid @enderror"
                         name="password" placeholder="••••••••" required>
                  @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              {{-- Remember Me & Forgot --}}
              <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="remember" id="remember">
                  <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
              </div>

              {{-- Submit --}}
              <div class="d-grid">
                <button id="submit" type="submit"
                        class="btn btn-primary btn-lg d-flex justify-content-center align-items-center">
                  <span>Login</span>
                  <div id="spinner" class="spinner-border spinner-border-sm ms-2" role="status" style="display: none;">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </button>
              </div>
            </form>
          </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-muted mt-4 small">
          Developed by
          <a href="https://fahimanzam.netlify.app" class="text-decoration-none">Vincent Peter</a>
        </p>
      </div>
    </div>
  </div>

  <!-- CoreUI JS -->
  <script src="{{ mix('js/app.js') }}" defer></script>
  <script>
    const loginForm = document.getElementById('login');
    const submitBtn = document.getElementById('submit');
    const spinner   = document.getElementById('spinner');

    loginForm.addEventListener('submit', () => {
      submitBtn.disabled = true;
      spinner.style.display = 'inline-block';
    });
  </script>
</body>
</html>
