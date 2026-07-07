<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SakuRame - Sistem Tabungan Digital Sekolah</title>

    <meta name="theme-color" content="#6777ef"/>
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">

    <!-- Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
        /* ===== CSS VARIABLES ===== */
    :root {
        --primary: #485CBC;
        --secondary: #3d4e9e;
        --accent: #FFB84C;
        --dark: #1e293b;
        --light: #f8fafc;
    }

    /* ===== BASE STYLES ===== */
    body {
        font-family: system-ui, sans-serif;
        background: var(--light);
        color: var(--dark);
        line-height: 1.6;
        margin: 0;
    }

    h1, h2, h3, h4, h5, h6 {
        font-weight: 700;
        margin: 0 0 1rem;
    }

    /* ===== BUTTONS ===== */
    .btn {
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 0.75rem;
        border: 1px solid;
        text-decoration: none;
        display: inline-block;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .btn-back {
        background: none;
        border: none;
        color: #475569;
        font-size: 1.2rem;
        padding: 0.5rem;
        border-radius: 0.75rem;
        cursor: pointer;
    }

    .btn-back:hover {
        background-color: var(--light);
    }

    /* ===== NAVIGATION ===== */
    .navbar {
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 0.8rem 0;
    }

    .nav-link {
        color: #475569;
        font-weight: 500;
        margin: 0 0.8rem;
        text-decoration: none;
    }

    .nav-link:hover,
    .nav-link.active {
        color: var(--primary);
    }

    /* ===== HERO SECTION ===== */
    .hero {
        padding: 160px 0 100px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }

    /* ===== LOGO ===== */
    .logo-circle {
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .logo-text {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--dark);
    }

    /* ===== CARDS ===== */
    .feature-card,
    .step-card,
    .testimonial-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        border: 1px solid rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin-bottom: 1.5rem;
    }

    /* ===== STEP CARDS ===== */
    .step-card {
        position: relative;
    }

    .step-number {
        position: absolute;
        top: -15px;
        left: -15px;
        width: 40px;
        height: 40px;
        background: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
    }

    /* ===== TESTIMONIALS ===== */
    .testimonial-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    .rating {
        color: var(--accent);
    }

    /* ===== MINI STATS ===== */
    .mini-stats-container {
        width: 100%;
        padding: 10px;
    }

    .mini-stat-card {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        padding: 10px;
        height: 100%;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .mini-stat-card .title {
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 3px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .mini-stat-card .value {
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: white;
    }

    .mini-stat-card .subtitle {
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.7);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ===== ACCORDION ===== */
    .accordion-item {
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.75rem;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .accordion-button {
        background: white;
        font-weight: 600;
        box-shadow: none;
        border: none;
        width: 100%;
        text-align: left;
        padding: 1rem;
        cursor: pointer;
    }

    .accordion-button:not(.collapsed) {
        color: var(--primary);
    }

    /* ===== LOGIN FORM ===== */
    .login-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        border: 1px solid rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }

    .login-header {
        text-align: left;
        margin-bottom: 2rem;
    }

    .welcome-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
        color: #475569;
        font-size: 0.9rem;
        margin-bottom: 0;
    }

    /* Login Form Elements */
    .login-form {
        width: 100%;
    }

    .login-form-group {
        margin-bottom: 1.5rem;
    }

    .login-form-label {
        display: block;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .login-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        background: #fff;
    }

    .login-input-wrapper:hover {
        transform: translateY(-2px);
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(72, 92, 188, 0.15);
    }

    .login-input-icon {
        position: absolute;
        left: 1rem;
        color: #475569;
        font-size: 0.9rem;
        z-index: 2;
    }

    .login-form-input {
        width: 100%;
        padding: 1rem 1rem 1rem 2.5rem;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.75rem;
        font-size: 0.9rem;
        background-color: var(--light);
        font-family: system-ui, sans-serif;
    }

    .login-form-input:focus {
        outline: none;
        border-color: var(--primary);
        background-color: white;
        box-shadow: 0 0 0 3px rgba(72, 92, 188, 0.1);
    }

    .login-form-input::placeholder {
        color: #94a3b8;
    }

    .login-password-toggle {
        position: absolute;
        right: 1rem;
        background: none;
        border: none;
        color: #475569;
        cursor: pointer;
        font-size: 0.9rem;
        z-index: 2;
    }

    .login-password-toggle:hover {
        color: var(--primary);
    }

    .login-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        border-radius: 0.75rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 1.5rem;
        font-family: system-ui, sans-serif;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-btn:hover {
        background: var(--secondary);
        transform: translateY(-2px);
    }

    .login-forgot-password {
        text-align: center;
    }

    .login-forgot-password a {
        color: #475569;
        text-decoration: none;
        font-size: 0.85rem;
    }

    .login-forgot-password a:hover {
        color: var(--primary);
    }

    /* ===== FOOTER ===== */
    .footer {
        background: var(--dark);
        color: white;
        padding: 80px 0 30px;
    }

    .footer-links h5 {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .footer-links h5::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -8px;
        width: 30px;
        height: 2px;
        background: var(--accent);
    }

    .footer-links ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.8rem;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
    }

    .footer-links a:hover {
        color: white;
    }

    .social-icons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        margin-right: 0.5rem;
        text-decoration: none;
    }

    .social-icons a:hover {
        background: var(--accent);
    }

    .copyright {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 1.5rem;
        margin-top: 3rem;
    }

    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 768px) {
        .hero {
            padding: 100px 0 60px;
        }

        .display-3 {
            font-size: 2.5rem;
        }

        .display-5 {
            font-size: 2rem;
        }

        .nav-link {
            margin: 0 0.4rem;
        }

        .login-card {
            margin: 1rem;
            padding: 1.5rem;
        }

        .scale-down-mobile {
            transform: scale(0.7);
        }

        .welcome-title {
            font-size: 1.3rem;
        }
    }

    @media (max-width: 576px) {
        .hero {
            padding: 90px 0 50px;
        }

        .display-3 {
            font-size: 2rem;
        }

        .feature-card,
        .step-card,
        .testimonial-card {
            padding: 1rem;
        }

        .mini-stat-card {
            padding: 10px;
        }

        .mini-stat-card .title,
        .mini-stat-card .subtitle {
            font-size: 0.55rem;
        }

        .mini-stat-card .value {
            font-size: 0.8rem;
        }

        .login-card {
            padding: 1rem;
        }

        .welcome-title {
            font-size: 1.2rem;
        }
    }
</style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <!-- Navbar Logo -->
            <a class="navbar-brand" href="#">
                <img id="logo-img" src="{{ asset('/dist/assets/compiled/svg/Logo Dark.svg') }}" height="40px" width="190px" alt="Logo">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#fitur">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cara-kerja">Cara Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item ms-3">
                        <button id="installAppBtn" class="btn btn-primary px-4">
                            <i class="fas fa-sign-in-alt me-2"></i>Install Aplikasi
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section dengan Mini Card Transparan -->
    <section class="hero text-white py-7 py-lg-8">
        <div class="container">
            <div class="row align-items-center">
                <!-- Login Form Section (Pindah ke atas di ponsel) -->
                <div class="col-lg-6 order-2 order-lg-1 mb-3 mb-lg-0 d-flex align-items-center justify-content-center" data-aos="fade-left">
                    <div class="login-card">
                        <!-- Header dengan Logo dan Judul -->
                        <div class="login-header">
                            <h2 class="welcome-title">Selamat Datang</h2>
                            <p class="welcome-subtitle">Silahkan login dengan akun anda.</p>
                        </div>

                        <!-- Alert Error -->
                        @if(session()->has('LoginError'))
                            <div class="alert alert-danger d-flex align-items-center">
                                <ion-icon name="alert-circle-outline" style="font-size: 1.3rem;"></ion-icon>
                                <span class="ms-2">{{ session('LoginError') }}</span>
                            </div>
                        @endif

                        <!-- Alert Logout Success -->
                        @if(session()->has('logoutSuccess'))
                            <script>
                                localStorage.removeItem("pwaPopupShown");
                            </script>
                            <div class="alert alert-success d-flex align-items-center">
                                <ion-icon name="checkmark-circle-outline" style="font-size: 1.3rem;"></ion-icon>
                                <span class="ms-2">Berhasil Logout!</span>
                            </div>
                        @endif

                        <!-- Form Login -->
                        <form action="{{ route('getin') }}" method="POST" class="login-form">
                            @csrf

                            @auth
                                <a href="{{ route(Auth::user()->roles_id == 1 ? 'kepsek.dashboard' : (Auth::user()->roles_id == 2 ? 'bendahara.dashboard' : (Auth::user()->roles_id == 3 ? 'walikelas.dashboard' : 'siswa.dashboard'))) }}" class="btn btn-secondary w-100">
                                    Masuk ke Dashboard
                                </a>
                            @else

                                <div class="login-form-group">
                                    <label class="login-form-label">Username / ID Tabungan</label>
                                    <div class="login-input-wrapper">
                                        <i class="fas fa-user login-input-icon"></i>
                                        <input type="text" class="login-form-input" name="username"
                                            placeholder="Masukkan username atau ID" required>
                                    </div>
                                </div>

                                <div class="login-form-group">
                                    <label class="login-form-label">Password</label>
                                    <div class="login-input-wrapper position-relative">
                                        <i class="fas fa-lock login-input-icon"></i>
                                        <input type="password" class="login-form-input" name="password"
                                            placeholder="••••••••" required id="passwordInput">
                                        <button type="button" class="login-password-toggle" id="togglePassword" style="background: none; border: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                                            <i class="fas fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="login-btn">
                                    Masuk
                                </button>
                            @endauth

                            <div class="login-forgot-password mt-2 text-center">
                                <a href="#">
                                    Lupa Password? Segera Hubungi Bendahara.
                                </a>
                            </div>
                        </form>

                    </div>
                </div>

                <!-- Text Section -->
                <div class="col-lg-6 order-2 order-lg-2 text-center text-lg-start" data-aos="fade-right">
                    <div class="badge bg-white text-primary mb-4 px-3 py-2 rounded-pill d-inline-block mobile-responsive-badge d-none d-md-inline-block">
                        🚀 Solusi Tabungan Digital Terdepan
                    </div>
                    <button id="installAppBtn2" class="btn badge bg-white text-primary mb-4 px-3 py-2 rounded-pill d-inline-block mobile-responsive-badge d-md-none">
                        🚀 Biar Sat-Set Klik Disini dan Install Aplikasi Ini
                    </button>
                    <h1 class="display-5 fw-bold mb-3 d-none d-lg-block">
                        Kelola Tabungan Siswa<br>
                        <span class="text-warning">Secara Modern</span>
                    </h1>
                    <p class="lead mb-4 opacity-75 px-2 px-lg-0 d-none d-lg-block">
                        Sistem tabungan digital terpercaya untuk SDN Sukarame yang memudahkan siswa, guru, dan orang tua.
                    </p>

                </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <!-- Company Info -->
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <img id="logo-img" src="{{ asset('/dist/assets/compiled/svg/Logo Light.svg') }}" class="mb-2" height="40px" width="190px" alt="Logo">
                    <p class="opacity-75 mb-4">SakuRame adalah sistem pengelolaan tabungan digital untuk sekolah yang modern, aman, dan efisien.</p>
                    <div class="social-icons">
                        {{-- <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a> --}}
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-md-4 col-lg-2 mb-4 mb-lg-0">
                    <div class="footer-links">
                        <h5>Tautan</h5>
                        <ul>
                            <li><a href="#">Beranda</a></li>
                            <li><a href="#fitur">Fitur</a></li>
                            <li><a href="#cara-kerja">Cara Kerja</a></li>
                            <li><a href="#faq">FAQ</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Contact -->
                <div class="col-md-4 col-lg-4">
                    <div class="footer-links">
                        <h5>Kontak</h5>
                        <ul>
                            <li><i class="fas fa-map-marker-alt me-2"></i> Jl. Raya Sukanagara - Pagelaran, Km 8</li>
                            <li><i class="fas fa-phone me-2"></i> 0821-2156-8359 </li>
                            <li><i class="fas fa-envelope me-2"></i> sdnegrisukarame@gmail.com</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="copyright text-center">
                <p class="mb-0 opacity-75">© 2025 SakuRame. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("passwordInput");
        const eyeIcon = document.getElementById("eyeIcon");

        togglePassword.addEventListener("click", function () {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);

            // Toggle icon class
            eyeIcon.classList.toggle("fa-eye");
            eyeIcon.classList.toggle("fa-eye-slash");
        });
    });
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize AOS
            AOS.init({
                duration: 800,
                once: true,
                easing: 'ease-in-out-quad',
                offset: 100,
                disable: function () {
                    return window.innerWidth < 768;
                }
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Close mobile menu after click
            const navbarCollapse = document.querySelector('.navbar-collapse');
            const navLinks = document.querySelectorAll('.nav-link');

            if (navbarCollapse && typeof bootstrap !== 'undefined') {
                navLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        if (navbarCollapse.classList.contains('show')) {
                            new bootstrap.Collapse(navbarCollapse).hide();
                        }
                    });
                });
            }

            // Sticky navbar on scroll
            window.addEventListener('scroll', () => {
                const navbar = document.querySelector('.navbar');
                if (navbar) {
                    if (window.scrollY > 100) {
                        navbar.classList.add('navbar-scrolled');
                    } else {
                        navbar.classList.remove('navbar-scrolled');
                    }
                }
            });
        });
    </script>

    <script>
        let deferredPrompt;
        window.addEventListener("beforeinstallprompt", (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });
    </script>
    <script>
        document.getElementById("installAppBtn").addEventListener("click", async (e) => {
            e.preventDefault();
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === "accepted") {
                    console.log("User accepted the install prompt");
                } else {
                    console.log("User dismissed the install prompt");
                }
                deferredPrompt = null; // Hapus agar tidak bisa dipakai dua kali
            } else {
                alert("Install tidak tersedia di browser ini. Silakan gunakan Chrome atau Edge.");
            }
        });
    </script>
    <script>
        document.getElementById("installAppBtn2").addEventListener("click", async (e) => {
            e.preventDefault();
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === "accepted") {
                    console.log("User accepted the install prompt");
                } else {
                    console.log("User dismissed the install prompt");
                }
                deferredPrompt = null; // Hapus agar tidak bisa dipakai dua kali
            } else {
                alert("Install tidak tersedia di browser ini. Silakan gunakan Chrome atau Edge.");
            }
        });
    </script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    </script>

    <!-- Toggle Password Script -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>