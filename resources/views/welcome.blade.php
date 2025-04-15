<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SakuRame - Sistem Tabungan Digital Sekolah</title>

    <!-- Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #485CBC;
            --secondary: #3d4e9e;
            --accent: #FFB84C;
            --dark: #1e293b;
            --light: #f8fafc;
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
        }

        .btn {
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--secondary);
            border-color: var(--secondary);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .navbar {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-sm);
            padding: 0.8rem 0;
            transition: all 0.3s ease;
        }

        .navbar-scrolled {
            box-shadow: var(--shadow-md);
            padding: 0.6rem 0;
        }

        .nav-link {
            color: #475569 !important;
            font-weight: 500;
            position: relative;
            margin: 0 0.8rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient);
            transition: width 0.3s ease;
        }

        .nav-link.active::after,
        .nav-link:hover::after {
            width: 100%;
        }

        .hero {
            padding: 160px 0 100px;
            background: var(--gradient);
            clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 20%, rgba(255,255,255,0.1) 50%, transparent 80%);
            animation: shine 8s infinite linear;
        }

        .feature-card {
            background: white;
            border-radius: 1rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0,0,0,0.05);
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-0.5rem);
            box-shadow: var(--shadow-lg);
        }

        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-bottom: 1.5rem;
        }

        .step-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            position: relative;
            height: 100%;
        }

        .step-number {
            position: absolute;
            top: -15px;
            left: -15px;
            width: 40px;
            height: 40px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        .testimonial-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .rating {
            color: var(--accent);
        }

        .accordion-item {
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 0.75rem !important;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .accordion-button {
            background: white;
            font-weight: 600;
            box-shadow: none !important;
        }

        .accordion-button:not(.collapsed) {
            color: var(--primary);
        }

        .footer {
            background: #1e293b;
            color: white;
            padding: 80px 0 30px;
        }

        .footer-links h5 {
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
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
        }

        .footer-links li {
            margin-bottom: 0.8rem;
        }

        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s ease;
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
            background: rgba(255,255,255,0.1);
            color: white;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            background: var(--accent);
        }

        .copyright {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1.5rem;
            margin-top: 3rem;
        }

        @keyframes shine {
            0% { transform: translateX(-50%) rotate(45deg); }
            100% { transform: translateX(50%) rotate(45deg); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-1rem); }
        }

        @media (max-width: 991.98px) {
            .hero {
                padding: 120px 0 70px;
            }
        }

        @media (max-width: 767.98px) {
            .hero {
                padding: 100px 0 60px;
            }

            .display-3 {
                font-size: 2.5rem;
            }

            .display-5 {
                font-size: 2rem;
            }
        }

        @media (max-width: 575.98px) {
            .hero {
                padding: 90px 0 50px;
            }

            .display-3 {
                font-size: 2rem;
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
                        <a href="/login" class="btn btn-primary px-4">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="badge bg-white text-primary mb-4 px-3 py-2 rounded-pill">
                        🚀 Solusi Tabungan Digital Terdepan
                    </div>
                    <h1 class="display-3 fw-bold mb-3">
                        Kelola Tabungan Siswa<br>
                        <span class="text-warning">Secara Modern</span>
                    </h1>
                    <p class="lead mb-4 opacity-75">Sistem tabungan digital terpercaya untuk SDN Sukarame yang memudahkan siswa, guru, dan orang tua.</p>

                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="/login" class="btn btn-light btn-lg rounded-pill">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk Sekarang
                        </a>
                        <a href="#fitur" class="btn btn-outline-light btn-lg rounded-pill">
                            <i class="fas fa-info-circle me-2"></i>Pelajari
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0" data-aos="fade-left">
                    <svg viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg" class="img-fluid rounded-3 shadow-lg">
                        <style>
                            .device { fill: #fff; }
                            .screen { fill: #f8fafc; }
                            .chart { fill: var(--primary); }
                            .coins { fill: var(--accent); }
                        </style>
                        <rect x="50" y="50" width="500" height="300" rx="25" class="device"/>
                        <rect x="75" y="75" width="450" height="250" rx="15" class="screen"/>
                        <path d="M100 150h400v40H100z" class="chart" opacity="0.8"/>
                        <path d="M100 220h300v40H100z" class="chart" opacity="0.6"/>
                        <path d="M100 290h250v40H100z" class="chart" opacity="0.4"/>
                        <circle cx="450" cy="200" r="40" class="coins"/>
                        <text x="450" y="200" text-anchor="middle" fill="#fff" font-size="20" dy=".3em">IDR</text>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Section -->
    <section id="fitur" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="zoom-in">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">Keunggulan</span>
                <h2 class="display-5 fw-bold mb-3">Fitur Unggulan</h2>
                <p class="text-muted fs-5 mb-0 mx-auto" style="max-width: 700px;">Solusi lengkap untuk manajemen tabungan sekolah yang modern dan efisien</p>
            </div>

            <div class="row g-4">
                <!-- Fitur 1 -->
                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="feature-card p-4 h-100">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                            </svg>
                        </div>
                        <h4 class="mb-3">Keamanan Data</h4>
                        <p class="text-muted mb-0">Proteksi data dengan enkripsi tingkat tinggi dan sistem backup otomatis untuk keamanan maksimal.</p>
                    </div>
                </div>

                <!-- Fitur 2 -->
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card p-4 h-100">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-chart-line fs-4"></i>
                        </div>
                        <h4 class="mb-3">Laporan Real-time</h4>
                        <p class="text-muted mb-0">Pantau perkembangan tabungan siswa secara langsung dengan laporan visual yang mudah dipahami.</p>
                    </div>
                </div>

                <!-- Fitur 3 -->
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card p-4 h-100">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-bell fs-4"></i>
                        </div>
                        <h4 class="mb-3">Notifikasi Otomatis</h4>
                        <p class="text-muted mb-0">Sistem reminder untuk transaksi dan pembayaran yang tertunda serta pemberitahuan untuk orang tua.</p>
                    </div>
                </div>

                <!-- Fitur 4 -->
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card p-4 h-100">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-mobile-alt fs-4"></i>
                        </div>
                        <h4 class="mb-3">Akses Multi-Perangkat</h4>
                        <p class="text-muted mb-0">Akses sistem tabungan dari berbagai perangkat dengan tampilan yang responsif dan optimal.</p>
                    </div>
                </div>

                <!-- Fitur 5 -->
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card p-4 h-100">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-history fs-4"></i>
                        </div>
                        <h4 class="mb-3">Riwayat Transaksi</h4>
                        <p class="text-muted mb-0">Catat semua aktivitas tabungan dengan detail lengkap untuk transparansi dan kemudahan pelacakan.</p>
                    </div>
                </div>

                <!-- Fitur 6 -->
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card p-4 h-100">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-users fs-4"></i>
                        </div>
                        <h4 class="mb-3">Manajemen Pengguna</h4>
                        <p class="text-muted mb-0">Atur hak akses untuk admin, guru, siswa, dan orang tua dengan tingkat izin yang disesuaikan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="cara-kerja" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="zoom-in">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">Cara Kerja</span>
                <h2 class="display-5 fw-bold mb-3">Bagaimana SakuRame Bekerja?</h2>
                <p class="text-muted fs-5 mb-0 mx-auto" style="max-width: 700px;">Proses sederhana untuk mengelola tabungan siswa secara digital</p>
            </div>

            <div class="row g-4">
                <!-- Step 1 -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="text-center mb-3">
                            <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                                <circle cx="40" cy="40" r="38" stroke="var(--primary)" stroke-width="4" fill="none"/>
                                <path d="M50 35h-5v-5h-5v5h-5v5h5v5h5v-5h5v-5z" fill="var(--secondary)"/>
                                <path d="M30 50h20v5H30z" fill="var(--accent)"/>
                            </svg>
                            <h4>Registrasi</h4>
                        </div>
                        <p class="text-muted mb-0">Admin sekolah mendaftarkan siswa dan guru ke dalam sistem SakuRame.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="text-center mb-3">
                            <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                                <circle cx="40" cy="40" r="38" stroke="var(--primary)" stroke-width="4" fill="none"/>
                                <path d="M32 50h16M40 30v20M30 40h20" stroke="var(--secondary)" stroke-width="4"/>
                                <circle cx="40" cy="40" r="15" fill="var(--accent)" opacity="0.3"/>
                            </svg>
                            <h4>Setor Tabungan</h4>
                        </div>
                        <p class="text-muted mb-0">Siswa melakukan setoran tabungan yang dicatat langsung dalam sistem.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="text-center mb-3">
                            <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                                <circle cx="40" cy="40" r="38" stroke="var(--primary)" stroke-width="4" fill="none"/>
                                <path d="M28 40h24M28 50h16M28 30h10" stroke="var(--secondary)" stroke-width="4"/>
                                <rect x="45" y="25" width="10" height="10" fill="var(--accent)"/>
                            </svg>
                            <h4>Pantau Saldo</h4>
                        </div>
                        <p class="text-muted mb-0">Orang tua dan guru dapat memantau perkembangan tabungan secara real-time.</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <div class="text-center mb-3">
                            <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                                <circle cx="40" cy="40" r="38" stroke="var(--primary)" stroke-width="4" fill="none"/>
                                <path d="M40 30v20M30 40l10 10 10-10" stroke="var(--secondary)" stroke-width="4"/>
                                <path d="M30 50h20v5H30z" fill="var(--accent)"/>
                            </svg>
                            <h4>Penarikan Dana</h4>
                        </div>
                        <p class="text-muted mb-0">Proses penarikan tabungan yang aman dengan persetujuan orang tua/wali.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="zoom-in">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">FAQ</span>
                <h2 class="display-5 fw-bold mb-3">Pertanyaan Umum</h2>
                <p class="text-muted fs-5 mb-0 mx-auto" style="max-width: 700px;">Jawaban untuk pertanyaan yang sering ditanyakan</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion" data-aos="fade-up">
                        <!-- FAQ Item 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Bagaimana cara mendaftar di SakuRame?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p class="mb-0">Pendaftaran dilakukan oleh admin sekolah. Setiap siswa akan mendapatkan akun dengan username dan password yang dapat diubah saat pertama kali login.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Apakah tabungan siswa aman di SakuRame?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p class="mb-0">Ya, sangat aman. SakuRame menggunakan enkripsi data tingkat tinggi dan backup rutin untuk memastikan keamanan data dan transaksi keuangan.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Bagaimana cara memantau tabungan anak saya?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p class="mb-0">Orang tua akan diberikan akun khusus untuk memantau tabungan anak secara real-time, melihat riwayat transaksi, dan mendapatkan notifikasi aktivitas tabungan.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Item 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Kapan saya bisa melakukan penarikan tabungan?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p class="mb-0">Penarikan tabungan dapat dilakukan pada jadwal yang ditentukan sekolah dan memerlukan persetujuan orang tua/wali melalui sistem.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 text-white" style="background: var(--gradient);">
        <div class="container py-5 text-center">
            <h2 class="display-5 fw-bold mb-4" data-aos="zoom-in">Mulai Kelola Tabungan Siswa Sekarang</h2>
            <p class="fs-5 mb-5 opacity-75 mx-auto" style="max-width: 700px;" data-aos="zoom-in" data-aos-delay="100">
                Bergabunglah dengan ratusan sekolah yang telah menggunakan SakuRame untuk pengelolaan tabungan digital
            </p>
            <a href="/login" class="btn btn-light btn-lg px-5 fw-bold" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk Sekarang
            </a>
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
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
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
                            <li><i class="fas fa-phone me-2"></i> (012) 123-4567</li>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            easing: 'ease-in-out-quad',
            offset: 100,
            disable: window.innerWidth < 768
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

        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (navbarCollapse.classList.contains('show')) {
                    new bootstrap.Collapse(navbarCollapse).hide();
                }
            });
        });

        // Sticky navbar on scroll
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    </script>
</body>
</html>
