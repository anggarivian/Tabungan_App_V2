<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SakuRame - Sistem Tabungan Digital Sekolah</title>

    <meta name="theme-color" content="#485CBC"/>
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

    /* ===== HERO VISUAL (illustration + CTA, replaces the old inline login form) ===== */
    .hero-visual {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 1rem;
    }

    .coin-float {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle at 32% 30%, rgba(255,255,255,.9), rgba(255,184,76,.35) 55%, transparent 72%);
        opacity: .55;
        animation: heroDrift 9s ease-in-out infinite;
        z-index: 0;
        pointer-events: none;
    }

    .coin-float.a { width: 90px; height: 90px; top: -6px; left: 0; animation-delay: 0s; }
    .coin-float.b { width: 60px; height: 60px; bottom: 60px; right: -4px; animation-delay: 1.2s; }
    .coin-float.c { width: 44px; height: 44px; bottom: 0; left: 18%; animation-delay: 2.4s; }

    @keyframes heroDrift {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(8px, -14px); }
    }

    .hero-illustration {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 320px;
        margin-bottom: 1.75rem;
    }

    .piggy-bob {
        animation: piggyBob 3.4s ease-in-out infinite;
        transform-origin: center;
    }

    @keyframes piggyBob {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-7px); }
    }

    .coin-drop {
        animation: coinDrop 2.6s ease-in infinite;
        transform-origin: center;
    }

    .coin-drop.c2 { animation-delay: .7s; }
    .coin-drop.c3 { animation-delay: 1.4s; }

    @keyframes coinDrop {
        0%   { opacity: 0; transform: translate(0, -26px) rotate(-12deg); }
        25%  { opacity: 1; transform: translate(0, -8px) rotate(-4deg); }
        70%  { opacity: 1; transform: translate(0, 6px) rotate(4deg); }
        100% { opacity: 0; transform: translate(0, 14px) rotate(8deg); }
    }

    .sparkle {
        animation: sparkleTwinkle 2.2s ease-in-out infinite;
    }

    .sparkle.s2 { animation-delay: .8s; }
    .sparkle.s3 { animation-delay: 1.5s; }

    @keyframes sparkleTwinkle {
        0%, 100% { opacity: .25; transform: scale(.85); }
        50% { opacity: 1; transform: scale(1); }
    }

    .hero-cta {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        background: white;
        color: var(--primary);
        font-weight: 700;
        font-size: 1.02rem;
        padding: 0.9rem 2.1rem;
        border-radius: 50px;
        text-decoration: none;
        box-shadow: 0 12px 26px -8px rgba(0, 0, 0, 0.35);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hero-cta:hover {
        color: var(--primary);
        transform: translateY(-3px);
        box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.4);
    }

    .hero-cta i {
        transition: transform 0.3s ease;
    }

    .hero-cta:hover i {
        transform: translateX(4px);
    }

    .hero-alert {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 320px;
        border-radius: 0.75rem;
        padding: 0.8rem 1rem;
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        margin-bottom: 1.25rem;
        background: rgba(255, 255, 255, 0.95);
    }

    .hero-alert.alert-danger { color: #d32f2f; border-left: 4px solid #d32f2f; }
    .hero-alert.alert-success { color: #2e7d32; border-left: 4px solid #2e7d32; }

    @media (prefers-reduced-motion: reduce) {
        .coin-float, .piggy-bob, .coin-drop, .sparkle {
            animation: none !important;
        }
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

        .hero-illustration {
            max-width: 240px;
        }

        .hero-cta {
            padding: 0.8rem 1.75rem;
            font-size: 0.95rem;
        }

        .scale-down-mobile {
            transform: scale(0.7);
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

        .hero-illustration {
            max-width: 200px;
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

    <!-- Hero Section dengan Ilustrasi Tabungan -->
    <section class="hero text-white py-7 py-lg-8">
        <div class="container">
            <div class="row align-items-center">
                <!-- Hero Visual Section (Pindah ke atas di ponsel) -->
                <div class="col-lg-6 order-2 order-lg-1 mb-4 mb-lg-0" data-aos="fade-left">
                    <div class="hero-visual">

                        <!-- Alert Error -->
                        @if(session()->has('LoginError'))
                            <div class="hero-alert alert-danger">
                                <i class="fas fa-circle-exclamation me-2"></i>
                                <span>{{ session('LoginError') }}</span>
                            </div>
                        @endif

                        <!-- Alert Logout Success -->
                        @if(session()->has('logoutSuccess'))
                            <script>
                                localStorage.removeItem("pwaPopupShown");
                            </script>
                            <div class="hero-alert alert-success">
                                <i class="fas fa-circle-check me-2"></i>
                                <span>Berhasil Logout!</span>
                            </div>
                        @endif

                        <span class="coin-float a"></span>
                        <span class="coin-float b"></span>
                        <span class="coin-float c"></span>

                        <!-- Ilustrasi 2D: celengan & koin -->
                        <svg class="hero-illustration" viewBox="0 0 420 340" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustrasi celengan dan koin tabungan">
                            <ellipse cx="215" cy="292" rx="118" ry="16" fill="rgba(15,23,42,0.08)"/>

                            <g class="coin-drop c1">
                                <circle cx="190" cy="82" r="17" fill="var(--accent)"/>
                                <circle cx="190" cy="82" r="17" fill="none" stroke="#C9861E" stroke-width="2"/>
                                <line x1="181" y1="82" x2="199" y2="82" stroke="#C9861E" stroke-width="2" stroke-linecap="round"/>
                            </g>
                            <g class="coin-drop c2">
                                <circle cx="219" cy="58" r="13" fill="var(--accent)"/>
                                <circle cx="219" cy="58" r="13" fill="none" stroke="#C9861E" stroke-width="1.6"/>
                                <line x1="213" y1="58" x2="225" y2="58" stroke="#C9861E" stroke-width="1.6" stroke-linecap="round"/>
                            </g>
                            <g class="coin-drop c3">
                                <circle cx="246" cy="88" r="11" fill="var(--accent)"/>
                                <circle cx="246" cy="88" r="11" fill="none" stroke="#C9861E" stroke-width="1.4"/>
                                <line x1="241" y1="88" x2="251" y2="88" stroke="#C9861E" stroke-width="1.4" stroke-linecap="round"/>
                            </g>

                            <g class="piggy-bob">
                                <!-- kaki -->
                                <rect x="138" y="245" width="18" height="30" rx="8" fill="var(--secondary)"/>
                                <rect x="182" y="252" width="16" height="24" rx="8" fill="var(--secondary)"/>
                                <rect x="255" y="252" width="16" height="24" rx="8" fill="var(--secondary)"/>
                                <rect x="296" y="245" width="18" height="30" rx="8" fill="var(--secondary)"/>

                                <!-- ekor -->
                                <path d="M318 178 Q340 168 332 152 Q326 142 336 136" stroke="var(--secondary)" stroke-width="6" fill="none" stroke-linecap="round"/>

                                <!-- badan -->
                                <ellipse cx="220" cy="195" rx="118" ry="80" fill="var(--primary)"/>

                                <!-- highlight perut -->
                                <ellipse cx="240" cy="225" rx="70" ry="34" fill="rgba(255,255,255,0.15)"/>

                                <!-- telinga -->
                                <path d="M170 130 L157 90 L200 122 Z" fill="var(--secondary)"/>
                                <path d="M215 120 L212 84 L245 112 Z" fill="var(--secondary)"/>

                                <!-- lubang koin -->
                                <rect x="196" y="118" width="46" height="10" rx="5" fill="#1e293b" opacity="0.55"/>

                                <!-- moncong -->
                                <ellipse cx="105" cy="205" rx="34" ry="26" fill="var(--secondary)"/>
                                <circle cx="93" cy="205" r="4" fill="#1e293b" opacity="0.5"/>
                                <circle cx="115" cy="205" r="4" fill="#1e293b" opacity="0.5"/>

                                <!-- mata -->
                                <path d="M148 168 Q155 161 162 168" stroke="#1e293b" stroke-width="4" fill="none" stroke-linecap="round"/>

                                <!-- pipi -->
                                <ellipse cx="150" cy="190" rx="10" ry="6" fill="var(--accent)" opacity="0.35"/>
                            </g>

                            <g class="sparkle s1" fill="var(--accent)">
                                <path d="M95 140 l4 10 10 4 -10 4 -4 10 -4 -10 -10 -4 10 -4 z"/>
                            </g>
                            <g class="sparkle s2" fill="var(--accent)">
                                <path d="M348 245 l3 7 7 3 -7 3 -3 7 -3 -7 -7 -3 7 -3 z"/>
                            </g>
                            <g class="sparkle s3" fill="var(--accent)">
                                <path d="M75 230 l3 7 7 3 -7 3 -3 7 -3 -7 -7 -3 7 -3 z"/>
                            </g>
                        </svg>

                        @auth
                            <a href="{{ route(Auth::user()->roles_id == 1 ? 'kepsek.dashboard' : (Auth::user()->roles_id == 2 ? 'bendahara.dashboard' : (Auth::user()->roles_id == 3 ? 'walikelas.dashboard' : 'siswa.dashboard'))) }}" class="hero-cta">
                                Lanjut ke Dashboard
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        @else
                            <a href="/login" class="hero-cta">
                                Masuk ke Akun
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        @endauth

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
</body>
</html>