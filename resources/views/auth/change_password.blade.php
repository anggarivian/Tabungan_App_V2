<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - SakuRame</title>

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
        /* Force light theme regardless of system/parent theme */
        :root {
            --navy-950: #0A1128 !important;
            --blue-800: #1E2F87 !important;
            --blue-600: #2E45C7 !important;
            --blue-500: #4361EE !important;
            --blue-400: #6C87F5 !important;
            --blue-200: #C4D0FF !important;
            --sky-100: #EEF2FF !important;
            --gold-400: #FFC24B !important;
            --paper: #FBFCFF !important;
            --ink-800: #1B2340 !important;
            --ink-500: #5C6584 !important;
            --success: #1C8F6B !important;
        }

        * { box-sizing: border-box; }

        /* Override any parent dark mode settings */
        * { color-scheme: light !important; }

        body {
            user-select: none;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 40px 16px;
            position: relative;
            background: var(--navy-950) !important;
            background-image:
                radial-gradient(circle at 12% 15%, rgba(108, 135, 245, .5), transparent 42%),
                radial-gradient(circle at 90% 10%, rgba(255, 194, 75, .2), transparent 36%),
                radial-gradient(circle at 82% 92%, rgba(46, 69, 199, .6), transparent 46%),
                linear-gradient(160deg, var(--navy-950) 0%, var(--blue-800) 48%, var(--blue-600) 100%) !important;
        }

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
            opacity: .45;
            background: radial-gradient(circle at 32% 30%, rgba(255,255,255,.85), rgba(255, 194, 75, .32) 55%, transparent 72%);
            animation: drift 11s ease-in-out infinite;
        }

        .coin-a { width: 130px; height: 130px; top: 5%; left: 5%; animation-delay: 0s; }
        .coin-b { width: 80px; height: 80px; top: 55%; left: 3%; animation-delay: 1.6s; }
        .coin-c { width: 170px; height: 170px; bottom: 4%; right: 6%; animation-delay: .7s; }

        @keyframes drift {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(10px, -22px); }
        }

        .screen-1 {
            position: relative;
            z-index: 1;
            background: rgba(251, 252, 255, .92) !important;
            backdrop-filter: blur(22px) saturate(160%);
            -webkit-backdrop-filter: blur(22px) saturate(160%);
            border: 1px solid rgba(255, 255, 255, .65);
            padding: 38px 34px 32px;
            display: flex;
            flex-direction: column;
            border-radius: 28px;
            box-shadow: 0 30px 60px -18px rgba(10, 17, 40, .5), 0 10px 24px rgba(10, 17, 40, .2);
            width: min(500px, 100%);
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 18px;
        }

        .logo-ring {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            padding: 4px;
            background: conic-gradient(from 0deg, var(--blue-500), var(--gold-400), var(--blue-400), var(--blue-500));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 22px -8px rgba(67, 97, 238, .5);
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

        .logo img { width: 50px; height: auto; }

        .form-title {
            font-family: 'Plus Jakarta Sans', 'Nunito', sans-serif;
            text-align: center;
            margin-top: 0;
            margin-bottom: 6px;
            color: var(--ink-800) !important;
            font-weight: 800;
            font-size: 1.65rem;
        }

        .form-subtitle {
            text-align: center;
            color: var(--ink-500) !important;
            font-size: .93rem;
            margin: 0 0 8px;
        }

        .section-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
            margin-bottom: 14px;
        }

        .section-head:first-of-type { margin-top: 22px; }

        .section-badge {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--blue-600), var(--blue-400));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 6px 14px -4px rgba(67, 97, 238, .5);
        }

        .section-badge ion-icon { color: #fff; margin: 0; font-size: 1.05rem; }

        .section-title {
            font-weight: 800;
            color: var(--ink-800) !important;
            font-size: 1.05rem;
        }

        .section-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, #E3E8FA, transparent);
            margin-left: 4px;
        }

        .info-text {
            font-size: .85rem;
            color: var(--ink-500) !important;
            margin-bottom: 16px;
            padding: 10px 14px;
            background-color: var(--sky-100) !important;
            border-radius: 12px;
            border-left: 3px solid var(--blue-500) !important;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .info-text ion-icon { margin: 2px 0 0; flex-shrink: 0; }

        .form-group { margin-bottom: 18px; }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 16px;
        }

        .input-field {
            background: white !important;
            box-shadow: 0 4px 12px rgba(10, 17, 40, .04);
            padding: 12px 15px;
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            color: var(--ink-800) !important;
            border: 1.5px solid #E3E8FA !important;
            transition: border-color .25s ease, box-shadow .25s ease, transform .15s ease;
        }

        .input-field:focus-within {
            border-color: var(--blue-500) !important;
            box-shadow: 0 0 0 4px rgba(67, 97, 238, .15) !important;
            transform: translateY(-1px);
        }

        label {
            font-weight: 700;
            font-size: .74rem;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: var(--blue-600) !important;
            margin-bottom: 6px;
        }

        .sec-2 { position: relative; display: flex; align-items: center; }

        ion-icon {
            color: var(--blue-500) !important;
            margin-right: 10px;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .input-field input, .input-field textarea {
            outline: none;
            border: none;
            width: 100%;
            font-size: .96rem;
            padding: 6px 0;
            color: var(--ink-800) !important;
            background: transparent !important;
            font-family: 'Nunito', sans-serif;
        }

        textarea { min-height: 64px; resize: vertical; font-family: 'Nunito', sans-serif; }

        .input-field input::placeholder, .input-field textarea::placeholder {
            color: #a4acc9 !important;
            font-size: .9rem;
        }

        .show-hide { position: absolute; right: 0; cursor: pointer; margin-right: 0; }

        .submit-btn {
            padding: 15px;
            background: linear-gradient(135deg, var(--blue-600), var(--blue-500)) !important;
            color: white !important;
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
            margin-top: 26px;
            box-shadow: 0 14px 28px -10px rgba(67, 97, 238, .65);
        }

        .submit-btn ion-icon { color: #fff !important; margin: 0; font-size: 1.1rem; }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 32px -10px rgba(67, 97, 238, .75);
            filter: brightness(1.04);
        }

        .submit-btn:active { transform: translateY(0); }

        .secondary-btn {
            padding: 14px;
            background: transparent !important;
            color: var(--ink-500) !important;
            border: 1.5px solid #E3E8FA;
            border-radius: 16px;
            font-weight: 700;
            font-size: .95rem;
            cursor: pointer;
            transition: all .25s ease;
            width: 100%;
            margin-top: 12px;
            text-decoration: none;
            display: block;
            box-shadow: none;
        }

        .secondary-btn:hover {
            background: var(--sky-100) !important;
            border-color: var(--blue-200);
            color: var(--blue-600) !important;
            transform: none;
        }

        .alert {
            border-radius: 12px;
            padding: 12px 15px;
            font-size: .9rem;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #EFFBF5 !important;
            color: var(--success) !important;
            border-left: 4px solid var(--success) !important;
        }

        .invalid-feedback {
            color: #D8455B !important;
            font-size: .8rem;
            margin-top: 6px;
            padding-left: 4px;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, .55) !important;
            backdrop-filter: blur(6px);
            color: var(--blue-600) !important;
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
            background: rgba(255, 255, 255, .8) !important;
            transform: translateX(-3px);
        }

        .back-button ion-icon { margin: 0; font-size: 1.35rem; }

        @media (prefers-reduced-motion: reduce) {
            .coin { animation: none !important; }
        }

        @media (max-width: 420px) {
            .screen-1 { padding: 32px 22px 26px; }
            .grid-2 { grid-template-columns: 1fr; }
        }

        /* Override any parent container dark mode styles */
        [data-theme="dark"] .screen-1,
        .dark .screen-1,
        [data-bs-theme="dark"] .screen-1 {
            background: rgba(251, 252, 255, .92) !important;
            color: var(--ink-800) !important;
        }

        [data-theme="dark"] .input-field,
        .dark .input-field,
        [data-bs-theme="dark"] .input-field {
            background: white !important;
            color: var(--ink-800) !important;
        }

        [data-theme="dark"] .input-field input,
        [data-theme="dark"] .input-field textarea,
        .dark .input-field input,
        .dark .input-field textarea,
        [data-bs-theme="dark"] .input-field input,
        [data-bs-theme="dark"] .input-field textarea {
            color: var(--ink-800) !important;
            background: transparent !important;
        }
    </style>
</head>

<body>
    <script src="{{ asset('/dist/assets/static/js/initTheme.js') }}"></script>

    <div class="ambient" aria-hidden="true">
        <span class="coin coin-a"></span>
        <span class="coin coin-b"></span>
        <span class="coin coin-c"></span>
    </div>

    <div class="screen-1">
        <a class="back-button" href="/" aria-label="Kembali">
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

        <h1 class="form-title">Update Profil</h1>
        <p class="form-subtitle">Perbarui informasi akun anda untuk keamanan dan kenyamanan</p>

        @if(session('success'))
            <div class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                <ion-icon name="checkmark-circle-outline"></ion-icon>
                <span style="margin-left: 8px;">{{ session('alert-message') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <script>
                setTimeout(function() {
                    $('.alert').alert('close');
                }, {{ session('alert-duration') }});

                setTimeout(function() {
                    window.location.href = 'bendahara/dashboard';
                }, {{ session('alert-duration') }});
            </script>
        @endif

        <form action="{{ route ('change.password') }}" method="POST">
            @csrf

            <div class="section-head">
                <div class="section-badge"><ion-icon name="shield-checkmark-outline"></ion-icon></div>
                <span class="section-title">Ubah Password</span>
                <span class="section-line"></span>
            </div>

            <div class="form-group">
                <div class="input-field">
                    <label for="password">Password Baru</label>
                    <div class="sec-2">
                        <ion-icon name="shield-outline"></ion-icon>
                        <input type="password" name="password" id="password" placeholder="Masukkan password baru" required>
                        <ion-icon class="show-hide" name="eye-outline" onclick="togglePassword('password')"></ion-icon>
                    </div>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-field">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="sec-2">
                        <ion-icon name="shield-checkmark-outline"></ion-icon>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Konfirmasi password baru" required>
                        <ion-icon class="show-hide" name="eye-outline" onclick="togglePassword('password_confirmation')"></ion-icon>
                    </div>
                </div>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="section-head">
                <div class="section-badge"><ion-icon name="call-outline"></ion-icon></div>
                <span class="section-title">Informasi Kontak</span>
                <span class="section-line"></span>
            </div>
            <div class="info-text">
                <ion-icon name="information-circle-outline"></ion-icon>
                <span>Nomor kontak dan email digunakan untuk menerima informasi tabungan dan pemberitahuan penting.</span>
            </div>

            <div class="grid-2">
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="input-field">
                        <label for="kontak">Nomor Telepon</label>
                        <div class="sec-2">
                            <ion-icon name="call-outline"></ion-icon>
                            <input type="tel" name="kontak" id="kontak" placeholder="Nomor telepon aktif" value="{{ old('kontak', $user->kontak ?? '') }}">
                        </div>
                    </div>
                    @error('kontak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <div class="input-field">
                        <label for="email">Email</label>
                        <div class="sec-2">
                            <ion-icon name="mail-outline"></ion-icon>
                            <input type="email" name="email" id="email" placeholder="Alamat email aktif" value="{{ old('email', $user->email ?? '') }}">
                        </div>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="section-head">
                <div class="section-badge"><ion-icon name="people-outline"></ion-icon></div>
                <span class="section-title">Informasi Pribadi</span>
                <span class="section-line"></span>
            </div>
            <div class="info-text">
                <ion-icon name="information-circle-outline"></ion-icon>
                <span>Mohon periksa dan lengkapi nama orang tua dan alamat.</span>
            </div>

            <div class="form-group">
                <div class="input-field">
                    <label for="orang_tua">Nama Orang Tua</label>
                    <div class="sec-2">
                        <ion-icon name="people-outline"></ion-icon>
                        <input type="text" name="orang_tua" id="orang_tua" placeholder="Masukkan nama orang tua" value="{{ old('orang_tua', $user->orang_tua ?? '') }}">
                    </div>
                </div>
                @error('orang_tua')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <div class="input-field">
                    <label for="alamat">Alamat Detail</label>
                    <div class="sec-2">
                        <ion-icon name="home-outline"></ion-icon>
                        <textarea name="alamat" id="alamat" placeholder="Masukkan alamat lengkap">{{ old('address', $user->alamat ?? '') }}</textarea>
                    </div>
                </div>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="submit-btn">
                Simpan Perubahan
                <ion-icon name="checkmark-outline"></ion-icon>
            </button>
        </form>
        <a href="/bendahara/dashboard" class="secondary-btn text-center">Nanti Saja</a>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = event.currentTarget;

            if (passwordInput.getAttribute('type') === 'password') {
                passwordInput.setAttribute('type', 'text');
                passwordIcon.setAttribute('name', 'eye-off-outline');
            } else {
                passwordInput.setAttribute('type', 'password');
                passwordIcon.setAttribute('name', 'eye-outline');
            }
        }

        function goBack() {
            window.location.href = '/bendahara/dashboard';
        }
    </script>
</body>

</html>