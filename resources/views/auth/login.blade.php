<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SakuRame</title>

    <link rel="shortcut icon" href="{{ asset('dist/assets/compiled/svg/Logo.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/iconly.css') }}">
</head>

<body>
    <script src="{{ asset('/dist/assets/static/js/initTheme.js') }}"></script>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 d-none d-lg-block">
                <div id="auth-right">

                </div>
            </div>
            <div class="col-lg-7 col-12">
                <div id="auth-left">
                    <div class="p-1">
                        <div class="auth-logo mb-5">
                            <a href="/">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                        <h1 class="auth-title" style="font-size: 47px">Log in.</h1>
                        <p class="auth-subtitle mb-3" style="font-size: 24px">Log in dengan akun anda.</p>

                        @if(session()->has('LoginError'))
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-file-excel"></i>
                                {{ session('LoginError') }}
                            </div>
                        @endif
                        @if(session()->has('logoutSuccess'))
                            <script>
                                localStorage.removeItem("pwaPopupShown"); // Reset agar popup muncul lagi setelah login
                            </script>
                            <div class="alert alert-success" role="alert">
                                <i class="bi bi-check-circle"></i>
                                <span>Berhasil Logout !</span>
                            </div>
                        @endif
                        <form action="/login" method="POST">
                            @csrf
                            <div class="form-group position-relative has-icon-left mb-2">
                                <input type="text" class="form-control form-control-xl @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="Username" autofocus required>
                                <div class="form-control-icon">
                                    <i class="bi bi-person" style="margin-left: 5px"></i>
                                </div>
                                @error('username')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group position-relative has-icon-left mb-2">
                                <input type="password" class="form-control form-control-xl" name="password" placeholder="Password" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-shield-lock" style="margin-left: 5px"></i>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                @enderror
                            </div>
                            <button class="btn btn-primary btn-block btn-lg shadow-lg mt-2">Log in</button>
                        </form>
                        <div class="text-center mt-5 text-lg fs-4">
                            <h6 class="fst-italic fw-lighter">Jika Lupa Password Silahkan ke Bendahara</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
