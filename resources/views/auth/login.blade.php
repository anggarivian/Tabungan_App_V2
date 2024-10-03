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
                    <div class="p-5">
                        <div class="auth-logo">
                            <a href="/">
                                <i class="bi bi-arrow-left"></i>
                                <img src="{{ asset ('/dist/assets/compiled/svg/Logo Dark.svg')}}" alt="Logo">
                            </a>
                        </div>
                        <h1 class="auth-title">Log in.</h1>
                        <p class="auth-subtitle mb-5">Log in dengan akun anda.</p>

                        @if(session()->has('LoginError'))
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-file-excel"></i>
                                {{ session('LoginError') }}
                            </div>
                        @endif
                        @if(session()->has('logoutSuccess'))
                            <div class="alert alert-success" role="alert">
                                <i class="bi bi-check-circle"></i>
                                <span>Berhasil Logout !</span>
                            </div>
                        @endif
                        <form action="/login" method="POST">
                            @csrf
                            <div class="form-group position-relative has-icon-left mb-4">
                                <input type="text" class="form-control form-control-xl @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="Username" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-person" style="margin-left: 5px"></i>
                                </div>
                                @error('username')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group position-relative has-icon-left mb-4">
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
                            {{-- <div class="form-check form-check-lg d-flex align-items-end">
                                <input class="form-check-input me-2" type="checkbox" value="" id="flexCheckDefault">
                                <label class="form-check-label text-gray-600" for="flexCheckDefault">
                                    Keep me logged in
                                </label>
                            </div> --}}
                            <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>
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
