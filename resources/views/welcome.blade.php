<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Welcome - SakuRame</title>
        <!-- Favicon-->
        <link rel="shortcut icon" href="{{ asset('dist/assets/compiled/svg/Logo.svg') }}" type="image/x-icon">
        <!-- Custom Google font-->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    </head>
    <body class="d-flex flex-column h-100">
        <main class="flex-shrink-0">
            <!-- Navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
                <div class="container px-5">
                    <a class="navbar-brand" href="/">
                        <img src="{{ asset('/dist/assets/compiled/svg/Logo Dark.svg') }}" alt="Logo" height="40" width="180">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder">
                            <li class="nav-item"><a class="nav-link" href="/">Beranda</a></li>
                            <li class="nav-item"><a class="nav-link" href="/">Informasi</a></li>
                            <li class="nav-item" style="margin-left: 10px">
                                @if (Route::has('login'))
                                    <div class="sm:block">
                                        @auth
                                            @switch(auth()->user()->roles_id)
                                                @case(1)
                                                    <a href="{{ route('kepsek.dashboard') }}" class="btn btn-dark">Dashboard</a>
                                                    @break
                                                @case(2)
                                                    <a href="{{ route('bendahara.dashboard') }}" class="btn btn-dark">Dashboard</a>
                                                    @break
                                                @case(3)
                                                    <a href="{{ route('walikelas.dashboard') }}" class="btn btn-dark">Dashboard</a>
                                                    @break
                                                @case(4)
                                                    <a href="{{ route('siswa.dashboard') }}" class="btn btn-dark">Dashboard</a>
                                                    @break
                                                @default
                                                    <a href="{{ url('/home') }}" class="btn btn-dark">Home</a>
                                            @endswitch
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                                        @endauth
                                    </div>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Header-->
            <header class="py-5">
                <div class="container px-5 pb-5">
                    <div class="row gx-5 align-items-center">
                        <div class="col-xxl-5">
                            <!-- Header text content-->
                            <div class="text-center text-xxl-start">
                                <div class="badge bg-gradient-primary-to-secondary text-white mb-4"><div class="text-uppercase">mudah &middot; cepat &middot; praktis</div></div>
                                <div class="fs-3 fw-light text-muted">Sekolah Dasar Negeri Sukarame</div>
                                <h1 class="display-3 fw-bolder mb-5"><span class="text-gradient d-inline">Sistem Informasi Tabungan Siswa</span></h1>
                                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center justify-content-xxl-start mb-3">
                                    @if (Route::has('login'))
                                        <div class="sm:block">
                                            @auth
                                                @switch(auth()->user()->roles_id)
                                                    @case(1)
                                                        <a href="{{ route('kepsek.dashboard') }}" class="btn btn-dark btn-lg px-5 py-3 me-sm-3 fs-6 fw-bolder">Dashboard</a>
                                                        @break
                                                    @case(2)
                                                        <a href="{{ route('bendahara.dashboard') }}" class="btn btn-dark btn-lg px-5 py-3 me-sm-3 fs-6 fw-bolder">Dashboard</a>
                                                        @break
                                                    @case(3)
                                                        <a href="{{ route('walikelas.dashboard') }}" class="btn btn-dark btn-lg px-5 py-3 me-sm-3 fs-6 fw-bolder">Dashboard</a>
                                                        @break
                                                    @case(4)
                                                        <a href="{{ route('siswa.dashboard') }}" class="btn btn-dark btn-lg px-5 py-3 me-sm-3 fs-6 fw-bolder">Dashboard</a>
                                                        @break
                                                    @default
                                                        <a href="{{ url('/home') }}" class="btn btn-dark btn-lg px-5 py-3 me-sm-3 fs-6 fw-bolder">Home</a>
                                                @endswitch
                                            @else
                                                <a class="btn btn-primary btn-lg px-5 py-3 me-sm-3 fs-6 fw-bolder" href="{{ route('login') }}">Log in</a>
                                            @endauth
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-7">
                            <!-- Header profile picture-->
                            <div class="d-flex justify-content-center mt-5 mt-xxl-0">
                                <div class="profile bg-gradient-primary-to-secondary">
                                    <div class="mt-5">
                                        <img class="profile-img p-5" src="{{ asset('/asset/profile2.png') }}" alt="Profile">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- About Section-->
            <section class="bg-light py-5">
                <div class="container px-5">
                    <div class="row gx-5 justify-content-center">
                        <div class="col-xxl-8">
                            <div class="text-center my-5">
                                <h2 class="display-5 fw-bolder"><span class="text-gradient d-inline">Tentang</span></h2>
                                <p class="lead fw-light mb-4">Sistem Informasi Tabungan Siswa di Sekolah Dasar Negeri Sukarame</p>
                                <p class="text-muted">Dibuat untuk memenuhi tugas akhir skripsi dan untuk mendigitalisasi proses tabungan sehingga lebih efektif dan efisien</p>
                                <div class="d-flex justify-content-center fs-2 gap-4">
                                    <a class="text-gradient" href="#!"><i class="bi bi-twitter"></i></a>
                                    <a class="text-gradient" href="#!"><i class="bi bi-linkedin"></i></a>
                                    <a class="text-gradient" href="#!"><i class="bi bi-github"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <!-- Footer-->
        <footer class="bg-white py-4 mt-auto">
            <div class="container px-5">
                <div class="row align-items-center justify-content-between flex-column flex-sm-row">
                    <div class="col-auto"><div class="small m-0">Copyright &copy; SDN Sukarame 2024</div></div>
                    <div class="col-auto">
                        <p class="m-0">Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a href="https://github.com/anggarivian">Angga</a></p>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="{{ asset('js/scripts.js') }}"></script>
    </body>
</html>
