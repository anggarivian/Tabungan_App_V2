<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SakuRame</title>

    <meta name="theme-color" content="#6777ef"/>
    <link rel="apple-touch-icon" href="{{ asset('Logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <link rel="shortcut icon" href="{{ asset('dist/assets/compiled/svg/Logo.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/iconly.css') }}">
    <link rel="preload" href="{{ asset('dist/assets/compiled/css/fonts/nunito-latin-300-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('dist/assets/compiled/css/fonts/nunito-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('dist/assets/compiled/css/fonts/nunito-latin-600-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('dist/assets/compiled/css/fonts/nunito-latin-700-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('dist/assets/compiled/css/fonts/nunito-latin-800-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <style>
        :root {
            --primary-color: #485cbc;
            --light-bg: #f8faff;
            --shadow-color: rgba(72, 92, 188, 0.2);
            --text-color: #333;
        }

        body {
            user-select: none;
            overflow-y: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--primary-color);
            background-image: linear-gradient(135deg, #485cbc 0%, #3a4ba3 100%);
            height: 100vh;
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
        }

        .screen-1 {
            background: var(--light-bg);
            padding: 35px 30px 30px;
            display: flex;
            flex-direction: column;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2), 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 330px;
            position: relative;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 120px;
            height: auto;
        }

        .form-title {
            text-align: center;
            margin-top: 5px;
            margin-bottom: 25px;
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .email, .password {
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 12px 15px;
            display: flex;
            flex-direction: column;
            border-radius: 12px;
            color: var(--text-color);
            transition: all 0.3s ease;
            border: 1px solid #e1e5f2;
        }

        .email:focus-within, .password:focus-within {
            box-shadow: 0 4px 12px rgba(72, 92, 188, 0.2);
            border-color: var(--primary-color);
        }

        label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #485cbc;
            margin-bottom: 6px;
        }

        .sec-2 {
            position: relative;
            display: flex;
            align-items: center;
        }

        ion-icon {
            color: var(--primary-color);
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .email input, .password input {
            outline: none;
            border: none;
            width: 100%;
            font-size: 0.95rem;
            padding: 6px 0;
            color: #333;
        }

        .email input::placeholder, .password input::placeholder {
            color: #aab;
            font-size: 0.9rem;
        }

        .show-hide {
            position: absolute;
            right: 5px;
            cursor: pointer;
            margin-right: 0;
        }

        .login {
            padding: 14px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 4px 10px rgba(72, 92, 188, 0.3);
        }

        .login:hover {
            background: #3a4ba3;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(72, 92, 188, 0.4);
        }

        .login:active {
            transform: translateY(0);
        }

        .footer {
            text-align: center;
            display: flex;
            font-size: 0.85rem;
            color: var(--text-color);
            justify-content: space-between;
            padding-top: 20px;
            margin-top: 10px;
            border-top: 1px solid #e1e5f2;
        }

        .alert {
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #fff2f2;
            color: #d32f2f;
            border-left: 4px solid #d32f2f;
        }

        .alert-success {
            background-color: #f0fff0;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        .text-danger {
            color: #d32f2f;
            font-size: 0.8rem;
            margin-top: 6px;
            padding-left: 30px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }

        .remember-me input {
            margin-right: 8px;
        }

        .remember-me label {
            font-size: 0.85rem;
            color: #666;
            font-weight: normal;
            cursor: pointer;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(72, 92, 188, 0.1);
            color: var(--primary-color);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .back-button:hover {
            background: rgba(72, 92, 188, 0.2);
            transform: translateX(-3px);
        }

        .back-button ion-icon {
            margin: 0;
            font-size: 1.4rem;
        }
        @keyframes fadeSlideIn {
            0% {
                opacity: 0;
                transform: translateY(40px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .screen-1 {
            animation: fadeSlideIn 0.8s ease-out;
        }

        .logo-container {
            animation: fadeSlideIn 1s ease-out;
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }

        .form-title {
            animation: fadeSlideIn 1s ease-out;
            animation-delay: 0.4s;
            animation-fill-mode: both;
        }

        form {
            animation: fadeSlideIn 1s ease-out;
            animation-delay: 0.6s;
            animation-fill-mode: both;
        }

        .footer {
            animation: fadeSlideIn 1s ease-out;
            animation-delay: 0.8s;
            animation-fill-mode: both;
        }

    </style>
</head>
<body>
    <div class="screen-1">
        <a href="/" class="back-button">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </a>

        <div class="logo-container">
            <div class="logo">
                <img src="{{ asset('/dist/assets/compiled/svg/Logo Dark.svg') }}" alt="Logo">
            </div>
        </div>

        <h3 class="form-title">Selamat Datang</h3>

        <p style="text-align: center;">Silahkan login dengan akun anda.</p>

        @if(session()->has('LoginError'))
            <div class="alert alert-danger">
                <ion-icon name="alert-circle-outline"></ion-icon>
                <span style="margin-left: 8px;">{{ session('LoginError') }}</span>
            </div>
        @endif

        @if(session()->has('logoutSuccess'))
            <script>
                localStorage.removeItem("pwaPopupShown");
            </script>
            <div class="alert alert-success">
                <ion-icon name="checkmark-circle-outline"></ion-icon>
                <span style="margin-left: 8px;">Berhasil Logout!</span>
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="form-group">
                <div class="email">
                    <label for="username">Username / ID Tabungan</label>
                    <div class="sec-2">
                        <ion-icon name="person-outline"></ion-icon>
                        <input type="text" name="username" id="username" placeholder="Masukkan username atau ID" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>
                @error('username')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="password">
                    <label for="password">Password</label>
                    <div class="sec-2">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input class="pas" type="password" name="password" id="password" placeholder="••••••••" required>
                        <ion-icon class="show-hide" name="eye-outline" onclick="togglePassword()"></ion-icon>
                    </div>
                </div>
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button class="login">Masuk</button>
        </form>

        <div class="footer">
            Lupa Password? Segera Hubungi Bendahara.
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.querySelector('.pas');
            const passwordIcon = document.querySelector('.show-hide');

            if (passwordInput.getAttribute('type') === 'password') {
                passwordInput.setAttribute('type', 'text');
                passwordIcon.setAttribute('name', 'eye-off-outline');
            } else {
                passwordInput.setAttribute('type', 'password');
                passwordIcon.setAttribute('name', 'eye-outline');
            }
        }

    </script>
    <script>
        if ("serviceWorker" in navigator) {
            window.addEventListener("load", () => {
            navigator.serviceWorker.register("/service-worker.js")
                .then(registration => {
                console.log("ServiceWorker registered with scope:", registration.scope);
                })
                .catch(error => {
                console.error("ServiceWorker registration failed:", error);
                });
            });
        }
    </script>

</body>
</html>
