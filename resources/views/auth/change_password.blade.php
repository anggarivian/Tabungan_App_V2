<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - SakuRame</title>

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
                            <a href="/bendahara/dashboard">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                        <h1 class="auth-title" style="font-size: 47px">Ubah Password</h1>
                        <p class="auth-subtitle mb-3" style="font-size: 24px">Ubah password anda</p>
                        @if(session('success'))
                            <div class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                                <strong>{{ session('alert-message') }}</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>

                            <script>
                                // Menutup alert setelah durasi tertentu
                                setTimeout(function() {
                                    $('.alert').alert('close');
                                }, {{ session('alert-duration') }});

                                // Redirect ke dashboard setelah durasi tertentu
                                setTimeout(function() {
                                    window.location.href = 'bendahara/dashboard'; // Ganti dengan route dashboard Anda
                                }, {{ session('alert-duration') }});
                            </script>
                        @endif

                        <form action="{{ route ('change.password.submit') }}" method="POST">
                            @csrf
                            <div class="form-group position-relative has-icon-left mb-2">
                                <input type="password" class="form-control form-control-xl" name="password" placeholder="Password Baru" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-shield-lock" style="margin-left: 5px"></i>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group position-relative has-icon-left mb-2">
                                <input type="password" class="form-control form-control-xl" name="password_confirmation" placeholder="Konfirmasi Password Baru" required>
                                <div class="form-control-icon">
                                    <i class="bi bi-shield-lock" style="margin-left: 5px"></i>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                @enderror
                            </div>
                            <button class="btn btn-primary btn-block btn-lg shadow-lg mt-2">Ubah Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
