<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SakuRame</title>

    <meta name="theme-color" content="#2E45C7"/>
    <link rel="apple-touch-icon" href="{{ asset('Logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">

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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy-950: #0A1128;
            --blue-800: #1E2F87;
            --blue-600: #2E45C7;
            --blue-500: #4361EE;
            --blue-400: #6C87F5;
            --blue-200: #C4D0FF;
            --sky-100: #EEF2FF;
            --gold-400: #FFC24B;
            --paper: #FBFCFF;
            --ink-800: #1B2340;
            --ink-500: #5C6584;
            --danger: #D8455B;
            --success: #1C8F6B;
        }

        * { box-sizing: border-box; }

        body {
            user-select: none;
            overflow-y: auto;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 32px 16px;
            position: relative;
            background: var(--navy-950);
            background-image:
                radial-gradient(circle at 14% 18%, rgba(108, 135, 245, .55), transparent 42%),
                radial-gradient(circle at 88% 12%, rgba(255, 194, 75, .22), transparent 38%),
                radial-gradient(circle at 78% 88%, rgba(46, 69, 199, .65), transparent 46%),
                linear-gradient(160deg, var(--navy-950) 0%, var(--blue-800) 48%, var(--blue-600) 100%);
        }

        /* ambient floating coin motifs */
        .ambient {
            position: fixed;
            inset: 0;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .coin {
            position: absolute;
            border-radius: 50%;
            opacity: .5;
            background: radial-gradient(circle at 32% 30%, rgba(255,255,255,.85), rgba(255, 194, 75, .35) 55%, transparent 72%);
            animation: drift 10s ease-in-out infinite;
        }

        .coin-a { width: 120px; height: 120px; top: 8%; left: 6%; animation-delay: 0s; }
        .coin-b { width: 70px; height: 70px; top: 68%; left: 12%; animation-delay: 1.4s; }
        .coin-c { width: 160px; height: 160px; top: 62%; right: 8%; animation-delay: .6s; }
        .coin-d { width: 54px; height: 54px; top: 14%; right: 16%; animation-delay: 2.2s; }

        @keyframes drift {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(10px, -20px); }
        }

        .screen-1 {
            position: relative;
            z-index: 1;
            background: rgba(251, 252, 255, .88);
            backdrop-filter: blur(22px) saturate(160%);
            -webkit-backdrop-filter: blur(22px) saturate(160%);
            border: 1px solid rgba(255, 255, 255, .65);
            padding: 38px 30px 30px;
            display: flex;
            flex-direction: column;
            border-radius: 28px;
            box-shadow: 0 30px 60px -18px rgba(10, 17, 40, .55), 0 10px 24px rgba(10, 17, 40, .22);
            width: min(360px, 100%);
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 18px;
        }

        .logo-ring {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            padding: 4px;
            background: conic-gradient(from 0deg, var(--blue-500), var(--gold-400), var(--blue-400), var(--blue-500));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px -8px rgba(67, 97, 238, .5);
        }

        .logo-ring-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            width: 56px;
            height: auto;
        }

        .eyebrow {
            display: block;
            text-align: center;
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--blue-500);
            margin-bottom: 6px;
        }

        .form-title {
            font-family: 'Plus Jakarta Sans', 'Nunito', sans-serif;
            text-align: center;
            margin-top: 0;
            margin-bottom: 6px;
            color: var(--ink-800);
            font-weight: 800;
            font-size: 1.55rem;
        }

        .form-subtitle {
            text-align: center;
            margin: 0 0 22px;
            color: var(--ink-500);
            font-size: .92rem;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .field {
            background: var(--paper);
            box-shadow: 0 4px 12px rgba(10, 17, 40, .04);
            padding: 12px 15px;
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            color: var(--ink-800);
            border: 1.5px solid #E3E8FA;
            transition: border-color .25s ease, box-shadow .25s ease, transform .15s ease;
        }

        .field:focus-within {
            border-color: var(--blue-500);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, .15);
            transform: translateY(-1px);
        }

        label {
            font-weight: 700;
            font-size: .74rem;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: var(--blue-600);
            margin-bottom: 6px;
        }

        .sec-2 {
            position: relative;
            display: flex;
            align-items: center;
        }

        ion-icon {
            color: var(--blue-500);
            margin-right: 10px;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .field input {
            outline: none;
            border: none;
            width: 100%;
            background: transparent;
            font-size: .96rem;
            padding: 6px 0;
            color: var(--ink-800);
            font-family: 'Nunito', sans-serif;
        }

        .field input::placeholder {
            color: #a4acc9;
            font-size: .9rem;
        }

        .show-hide {
            position: absolute;
            right: 0;
            cursor: pointer;
            margin-right: 0;
        }

        .login {
            padding: 15px;
            background: linear-gradient(135deg, var(--blue-600), var(--blue-500));
            color: white;
            border: none;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1rem;
            font-family: 'Nunito', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
            width: 100%;
            margin-top: 6px;
            box-shadow: 0 14px 28px -10px rgba(67, 97, 238, .65);
        }

        .login ion-icon { color: white; margin: 0; font-size: 1.1rem; }

        .login:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 32px -10px rgba(67, 97, 238, .75);
            filter: brightness(1.04);
        }

        .login:active { transform: translateY(0); }

        .footer {
            text-align: center;
            font-size: .84rem;
            color: var(--ink-500);
            justify-content: center;
            padding-top: 18px;
            margin-top: 20px;
            border-top: 1px solid #E3E8FA;
        }

        .alert {
            border-radius: 12px;
            padding: 12px 15px;
            font-size: .9rem;
            display: flex;
            align-items: center;
            margin-bottom: 18px;
        }

        .alert ion-icon { margin: 0; }

        .alert-danger {
            background-color: #FFF1F2;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .alert-success {
            background-color: #EFFBF5;
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .text-danger {
            color: var(--danger);
            font-size: .8rem;
            margin-top: 6px;
            padding-left: 4px;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, .55);
            backdrop-filter: blur(6px);
            color: var(--blue-600);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all .25s ease;
            border: 1px solid rgba(255, 255, 255, .7);
        }

        .back-button:hover {
            background: rgba(255, 255, 255, .8);
            transform: translateX(-3px);
        }

        .back-button ion-icon { margin: 0; font-size: 1.35rem; }

        @keyframes fadeSlideIn {
            0% { opacity: 0; transform: translateY(32px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .screen-1 { animation: fadeSlideIn .7s ease-out; }
        .logo-container { animation: fadeSlideIn .9s ease-out .15s both; }
        .form-title, .form-subtitle { animation: fadeSlideIn .9s ease-out .3s both; }
        form { animation: fadeSlideIn .9s ease-out .45s both; }
        .footer { animation: fadeSlideIn .9s ease-out .6s both; }

        @media (prefers-reduced-motion: reduce) {
            .coin, .screen-1, .logo-container, .form-title, .form-subtitle, form, .footer {
                animation: none !important;
            }
        }

        @media (max-width: 380px) {
            .screen-1 { padding: 32px 22px 24px; }
        }
    </style>
</head>
<body>

    <div class="ambient" aria-hidden="true">
        <span class="coin coin-a"></span>
        <span class="coin coin-b"></span>
        <span class="coin coin-c"></span>
        <span class="coin coin-d"></span>
    </div>

    <div class="screen-1">
        <a href="/" class="back-button" aria-label="Kembali">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </a>

        <div class="logo-container">
            <div class="logo-ring">
                <div class="logo-ring-inner">
                    <div class="logo">
                        <img src="{{ asset('/dist/assets/compiled/svg/Logo Dark.svg') }}" alt="Logo">
                    </div>
                </div>
            </div>
        </div>

        <span class="eyebrow">SakuRame &bull; Tabungan Digital</span>
        <h3 class="form-title">Selamat Datang</h3>
        <p class="form-subtitle">Silahkan login dengan akun anda.</p>

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
                <div class="field">
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
                <div class="field">
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

            <button class="login">
                Masuk
                <ion-icon name="arrow-forward-outline"></ion-icon>
            </button>
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