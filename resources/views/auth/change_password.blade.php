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
    <style>
        :root {
            --primary-color: #485cbc;
            --light-bg: #f8faff;
            --shadow-color: rgba(72, 92, 188, 0.2);
            --text-color: #333;
        }

        body {
            user-select: none;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--primary-color);
            background-image: linear-gradient(135deg, #485cbc 0%, #3a4ba3 100%);
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 20px 0;
        }

        .screen-1 {
            background: var(--light-bg);
            padding: 35px 30px 30px;
            display: flex;
            flex-direction: column;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2), 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 450px;
            position: relative;
            margin: 20px 0;
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
            margin-bottom: 10px;
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.7rem;
        }

        .form-subtitle {
            text-align: center;
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 25px;
        }

        .section-title {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.1rem;
            margin-top: 20px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e1e5f2;
            padding-bottom: 8px;
        }

        .info-text {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 15px;
            padding: 8px 12px;
            background-color: rgba(72, 92, 188, 0.05);
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .input-field {
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

        .input-field:focus-within {
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

        .input-field input, .input-field textarea {
            outline: none;
            border: none;
            width: 100%;
            font-size: 0.95rem;
            padding: 6px 0;
            color: #333;
            background: transparent;
        }

        textarea {
            min-height: 60px;
            resize: vertical;
            font-family: 'Nunito', sans-serif;
        }

        .input-field input::placeholder, .input-field textarea::placeholder {
            color: #aab;
            font-size: 0.9rem;
        }

        .show-hide {
            position: absolute;
            right: 5px;
            cursor: pointer;
            margin-right: 0;
        }

        .submit-btn {
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
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(72, 92, 188, 0.3);
        }

        .submit-btn:hover {
            background: #3a4ba3;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(72, 92, 188, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #f0fff0;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        .invalid-feedback {
            color: #d32f2f;
            font-size: 0.8rem;
            margin-top: 6px;
            padding-left: 30px;
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
    </style>
</head>

<body>
    <script src="{{ asset('/dist/assets/static/js/initTheme.js') }}"></script>
    <div class="screen-1">
        <button class="back-button" onclick="goBack()">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </button>

        <div class="logo-container">
            <div class="logo">
                <img src="{{ asset('/dist/assets/compiled/svg/Logo Dark.svg') }}" alt="Logo">
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
            <div class="section-title">Ubah Password</div>

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

            <div class="section-title">Informasi Kontak</div>
            <div class="info-text">
                <ion-icon name="information-circle-outline"></ion-icon>
                Nomor kontak dan email digunakan untuk menerima informasi tabungan dan pemberitahuan penting.
            </div>

            <div class="form-group">
                <div class="input-field">
                    <label for="kontak">Nomor Telepon</label>
                    <div class="sec-2">
                        <ion-icon name="call-outline"></ion-icon>
                        <input type="tel" name="kontak" id="kontak" placeholder="Masukkan nomor telepon aktif" value="{{ old('kontak', $user->kontak ?? '') }}">
                    </div>
                </div>
                @error('kontak')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-field">
                    <label for="email">Email</label>
                    <div class="sec-2">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="email" name="email" id="email" placeholder="Masukkan alamat email aktif" value="{{ old('email', $user->email ?? '') }}">
                    </div>
                </div>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="section-title">Informasi Pribadi</div>
            <div class="info-text">
                <ion-icon name="information-circle-outline"></ion-icon>
                Mohon periksa dan lengkapi nama orang tua dan alamat.
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

            <div class="form-group">
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

            <button type="submit" class="submit-btn">Simpan Perubahan</button>
        </form>
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
