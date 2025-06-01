<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- PWA -->
    <meta name="theme-color" content="#6777ef"/>
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

    <title>@yield('title')</title>
    {{-- @laravelPWA --}}
    @yield('style')

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link rel="preload" href="{{ asset('dist/assets/compiled/css/fonts/nunito-latin-300-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('dist/assets/compiled/css/fonts/nunito-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('dist/assets/compiled/css/fonts/nunito-latin-600-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('dist/assets/compiled/css/fonts/nunito-latin-700-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ asset('dist/assets/compiled/css/fonts/nunito-latin-800-normal.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous">

    <link rel="shortcut icon" href="{{ asset('/dist/assets/compiled/svg/Logo.svg') }}" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS Kustom -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('/dist/assets/compiled/css/iconly.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css">

    <style>
        @media (orientation: portrait) {
            .portrait-card {
                display: block !important;
            }
        }
        @media (orientation: landscape) {
            .portrait-card {
                display: none !important;
            }
        }
        /* Animasi untuk modal */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: scale(0.9);
        }
        .modal.show .modal-dialog {
            transform: scale(1);
        }

        /* Gaya tombol */
        .btn {
            transition: all 0.2s;
            font-weight: 500;
            border-radius: 8px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn-danger {
            background: linear-gradient(45deg, #ff4b4b, #ff6b6b);
            border: none;
        }
        .btn-light {
            background: #f8f9fa;
            border: 1px solid #eaeaea;
        }

        /* Efek shake saat hover pada icon hapus */
        .bi-trash-fill {
            transition: all 0.3s;
        }
        .bi-trash-fill:hover {
            animation: shake 0.5s;
        }

        @keyframes shake {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            50% { transform: rotate(5deg); }
            75% { transform: rotate(-5deg); }
            100% { transform: rotate(0deg); }
        }
        /* ======== DESKTOP DEFAULT (>=1201px) ======== */
        .main-navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: white;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            height: 40px;
            padding: 5px 0;
            font-size: 14px;
        }

        body {
            padding-top: 40px;
        }

        .main-navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .main-navbar a {
            padding: 5px 10px;
        }

        .main-navbar img {
            height: 30px;
            width: auto;
        }

        @media (max-width: 1199.98px) {
            .custom-margin-top-fix {
                margin-top: 0px !important;
            }
            main-navbar {
                position: absolute;
                top: 70px; /* sesuaikan dengan tinggi header */
                left: 0;
                width: 100%;
                background-color: white;
                max-height: 0;
                overflow: hidden;
                opacity: 0;
                transition: max-height 0.4s ease, opacity 0.4s ease;
                z-index: 999;
            }

            .main-navbar.show {
                max-height: 500px; /* sesuaikan dengan tinggi konten nav */
                opacity: 1;
            }
        }

        /* ======== TABLET BESAR / LAPTOP KECIL (992px - 1200px) ======== */
        @media (min-width: 992px) and (max-width: 1200px) {
            .main-navbar {
                height: auto;
                padding: 10px 0;
                margin-top: 60px;
                font-size: 15px;
            }

            .main-navbar img {
                height: 40px;
            }

            body {
                padding-top: 0 !important;
            }

            .navbar-collapse,
            .dropdown-menu {
                background-color: white;
                padding: 10px;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            }
        }

        /* ======== TABLET & PONSEL (<= 991px) ======== */
        @media (max-width: 991.98px) {
            .main-navbar {
                height: auto;
                padding: 10px 0;
                margin-top: 60px;
                font-size: 15px;
            }

            .main-navbar img {
                height: 40px;
            }

            body {
                padding-top: 0 !important;
            }

            .navbar-collapse,
            .dropdown-menu {
                background-color: white;
                padding: 10px;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            }
        }

        /* ======== HP KECIL (<= 575px) ======== */
        @media (max-width: 575.98px) {
            .main-navbar {
                font-size: 14px;
                padding: 12px 0;
            }

            .main-navbar img {
                height: 38px;
            }

            .dropdown-menu {
                font-size: 14px;
            }
        }

        #loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f4f4f4; /* atau dark: #0b0c10 */
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #loader {
            border: 6px solid #ddd;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Sembunyikan content-wrapper dulu */
        .content-wrapper {
            display: none;
        }

        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

    </style>

</head>


<body>
    <script src="{{ asset('/dist/assets/static/js/initTheme.js') }}"></script>
    <div id="app">
        <div id="main" class="layout-horizontal">
            <header class="mb-5">
                <div class="header-top shadow-sm">
                    <div class="container">
                        <a class="d-flex" href="{{ route(Auth::user()->roles_id == 1 ? 'kepsek.dashboard' : (Auth::user()->roles_id == 2 ? 'bendahara.dashboard' : (Auth::user()->roles_id == 3 ? 'walikelas.dashboard' : 'siswa.dashboard'))) }}">
                            <img id="logo-img" src="{{ asset('Logo Normal.svg') }}" height="40px" width="190px" alt="Logo">
                        </a>
                        <div class="header-top-right">
                            <div class="d-none d-md-flex">
                                <div class="text text-end">
                                    <h6 class="user-dropdown-name">{{ auth()->user()->name }}</h6>
                                    @php
                                        $roles = [
                                            1 => 'Kepala Sekolah',
                                            2 => 'Bendahara',
                                            3 => 'Walikelas',
                                            4 => 'Siswa'
                                        ];
                                    @endphp
                                    <p class="user-dropdown-status text-sm text-muted">
                                        {{ $roles[auth()->user()->roles_id] }}
                                        @if(auth()->user()->roles_id == 3)
                                            {{ auth()->user()->kelas->name }}
                                        @elseif(auth()->user()->roles_id == 4)
                                            Kelas
                                            {{ auth()->user()->kelas->name }}
                                        @endif
                                    </p>
                                </div>
                                <div class="avatar avatar-md2" style="margin-left: 20px">
                                    <img src="{{ asset('/dist/assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                </div>
                            </div>
                            <div class="d-md-none">
                                <div class="avatar avatar-md2">
                                    <img src="{{ asset('/dist/assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                </div>
                            </div>

                            <!-- Burger button responsive -->
                            <a href="#" class="burger-btn d-block d-xl-none">
                                <i class="bi bi-justify fs-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <nav class="main-navbar shadow-sm">
                    <div class="container">
                        <ul>
                            {{-- Dasdhboard -------------------------------------------------------------------------------------------------------------------- --}}
                            @if( auth()->user()->roles_id == 1 )
                            <li class="menu-item {{ request()->routeIs('kepsek.dashboard') ? 'active' : '' }}">
                                <a href="{{ route ('kepsek.dashboard')}}" class='menu-link' style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-speedometer2"></i> <span>Dashboard</span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 2)
                            <li class="menu-item {{ request()->routeIs('bendahara.dashboard') ? 'active' : '' }}">
                                <a href="{{ route ('bendahara.dashboard')}}" class='menu-link' style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-speedometer2"></i><span> Dashboard</span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 3)
                            <li class="menu-item {{ request()->routeIs('walikelas.dashboard') ? 'active' : '' }}">
                                <a href="{{ route ('walikelas.dashboard')}}" class='menu-link' style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-speedometer2"></i><span> Dashboard</span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 4)
                            <li class="menu-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                                <a href="{{ route ('siswa.dashboard')}}" class='menu-link' style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-speedometer2"></i><span>Dashboard</span>
                                </a>
                            </li>
                            @endif
                            {{-- Transaksi Tabungan -------------------------------------------------------------------------------------------------------------------- --}}
                            @if( auth()->user()->roles_id == 2 )
                            <li class="menu-item {{ request()->routeIs('bendahara.tabungan*') || request()->routeIs('tabungan.stor') || request()->routeIs('tabungan.tarik') ? 'active' : '' }}">
                                <a href="{{ route ('bendahara.tabungan.index')}}" class='menu-link' style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-cash-stack"></i><span> Tabungan </span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 3)
                            <li class="menu-item {{ request()->routeIs('walikelas.tabungan*') ? 'active' : '' }}">
                                <a href="{{ route ('walikelas.tabungan.index')}}" class='menu-link' style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-box-arrow-in-down"></i><span> Stor Tabungan </span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 4)
                            <li class="menu-item {{ request()->routeIs('siswa.tabungan.stor') ? 'active' : '' }}">
                                <a href="{{ route ('siswa.tabungan.stor')}}" class='menu-link' style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-box-arrow-in-down"></i><span> Stor Tabungan</span>
                                </a>
                            </li>
                            @endif
                            {{-- Pengajuan -------------------------------------------------------------------------------------------------------------------- --}}
                            @if( auth()->user()->roles_id == 2 )
                            <li class="menu-item {{ request()->routeIs('bendahara.pengajuan*') ? 'active' : '' }}">
                                <a href="{{ route ('bendahara.pengajuan.index')}}" class='menu-link' style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-journal-arrow-up"></i><span> Pengajuan </span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 4)
                            <li class="menu-item {{ request()->routeIs('siswa.tabungan.tarik*') ? 'active' : '' }}">
                                <a href="{{ route ('siswa.tabungan.tarik')}}" class='menu-link' style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-box-arrow-up"></i><span> Tarik Tabungan</span>
                                </a>
                            </li>
                            @endif
                            {{-- Laporan -------------------------------------------------------------------------------------------------------------------- --}}
                            @if( auth()->user()->roles_id == 1)
                            <li class="menu-item {{ request()->is('kepsek/laporan*') ? 'active' : '' }} has-sub">
                                <a href="#" class="menu-link" style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-bar-chart-line-fill"></i><span> Laporan</span>
                                </a>
                                <div class="submenu">
                                    <div class="submenu-group-wrapper">
                                        <ul class="submenu-group">
                                            <li class="submenu-item {{ request()->routeIs('laporan.kepsek.tabungan') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.kepsek.tabungan') }}" class="submenu-link">Laporan Tabungan</a>
                                            </li>
                                            <li class="submenu-item {{ request()->routeIs('laporan.kepsek.transaksi') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.kepsek.transaksi') }}" class="submenu-link">Laporan Transaksi</a>
                                            </li>
                                            <li class="submenu-item {{ request()->routeIs('laporan.kepsek.pengajuan') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.kepsek.pengajuan') }}" class="submenu-link">Laporan Pengajuan</a>
                                            </li>
                                            <li class="submenu-item {{ request()->routeIs('laporan.kepsek.export') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.kepsek.export') }}" class="submenu-link">Laporan Export</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            @elseif( auth()->user()->roles_id == 2 )
                            <li class="menu-item {{ request()->is('bendahara/laporan*') ? 'active' : '' }} has-sub">
                                <a href="#" class="menu-link" style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-bar-chart-line-fill"></i><span> Laporan</span>
                                </a>
                                <div class="submenu">
                                    <div class="submenu-group-wrapper">
                                        <ul class="submenu-group">
                                            <li class="submenu-item {{ request()->routeIs('laporan.bendahara.tabungan') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.bendahara.tabungan') }}" class="submenu-link">Laporan Tabungan</a>
                                            </li>
                                            <li class="submenu-item {{ request()->routeIs('laporan.bendahara.transaksi') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.bendahara.transaksi') }}" class="submenu-link">Laporan Transaksi</a>
                                            </li>
                                            <li class="submenu-item {{ request()->routeIs('laporan.bendahara.pengajuan') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.bendahara.pengajuan') }}" class="submenu-link">Laporan Pengajuan</a>
                                            </li>
                                            <li class="submenu-item {{ request()->routeIs('laporan.bendahara.export') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.bendahara.export') }}" class="submenu-link">Laporan Export</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            @elseif( auth()->user()->roles_id == 3)
                            <li class="menu-item {{ request()->is('walikelas/laporan*') ? 'active' : '' }} has-sub">
                                <a href="#" class="menu-link" style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-bar-chart-line-fill"></i><span> Laporan</span>
                                </a>
                                <div class="submenu">
                                    <div class="submenu-group-wrapper">
                                        <ul class="submenu-group">
                                            <li class="submenu-item {{ request()->routeIs('laporan.walikelas.tabungan') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.walikelas.tabungan') }}" class="submenu-link">Laporan Tabungan</a>
                                            </li>
                                            <li class="submenu-item {{ request()->routeIs('laporan.walikelas.transaksi') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.walikelas.transaksi') }}" class="submenu-link">Laporan Transaksi</a>
                                            </li>
                                            <li class="submenu-item {{ request()->routeIs('laporan.walikelas.export') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.walikelas.export') }}" class="submenu-link">Laporan Export</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            @elseif( auth()->user()->roles_id == 4)
                            <li class="menu-item {{ request()->is('siswa/laporan*') ? 'active' : '' }} has-sub">
                                <a href="#" class="menu-link" style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-bar-chart-line-fill"></i><span> Laporan</span>
                                </a>
                                <div class="submenu">
                                    <div class="submenu-group-wrapper">
                                        <ul class="submenu-group">
                                            {{-- <li class="submenu-item {{ request()->routeIs('laporan.siswa.tabungan') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.siswa.tabungan') }}" class="submenu-link">Laporan Tabungan</a>
                                            </li> --}}
                                            <li class="submenu-item {{ request()->routeIs('laporan.siswa.transaksi') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.siswa.transaksi') }}" class="submenu-link">Laporan Transaksi</a>
                                            </li>
                                            <li class="submenu-item {{ request()->routeIs('laporan.siswa.pengajuan') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.siswa.pengajuan') }}" class="submenu-link">Laporan Pengajuan</a>
                                            </li>
                                            <li class="submenu-item {{ request()->routeIs('laporan.siswa.export') ? 'active' : '' }}">
                                                <a href="{{ route('laporan.siswa.export') }}" class="submenu-link">Laporan Export</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            @endif
                            {{-- Siswa Walikelas -------------------------------------------------------------------------------------------------------------------- --}}
                            @if( auth()->user()->roles_id == 2 )
                            <li class="menu-item {{ request()->is('bendahara/kelola-*') ? 'active' : '' }} has-sub">
                                    <a href="#" class="menu-link" style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                        <i class="bi-person-lines-fill"></i><span> Data Pengguna</span>
                                    </a>
                                    <div class="submenu">
                                        <div class="submenu-group-wrapper">
                                            <ul class="submenu-group">
                                                {{-- Walikelas --}}
                                                <li class="submenu-item {{ request()->routeIs('bendahara.walikelas.index') ? 'active' : '' }}">
                                                    <a href="{{ route('bendahara.walikelas.index') }}" class="submenu-link">Walikelas</a>
                                                </li>
                                                {{-- Siswa --}}
                                                <li class="submenu-item {{ request()->routeIs('bendahara.siswa.index') ? 'active' : '' }}">
                                                    <a href="{{ route('bendahara.siswa.index') }}" class="submenu-link">Siswa</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            <li class="menu-item {{ request()->routeIs('profil*') ? 'active' : '' }}">
                                <a href="{{ route ('profil.edit')}}" class='menu-link' style="display: flex; align-items: center; gap: 10px; margin-top: -13px">
                                    <i class="bi-person-circle"></i><span>Profil</span>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="{{ route('logout') }}" class="menu-link" style="display: flex; align-items: center; gap: 10px; margin-top: -13px;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi-box-arrow-right"></i><span>Logout</span>
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                            <li class="menu-item ms-auto custom-margin-top-fix" style="margin-top: -13px;">
                                <div class="d-flex align-items-center px-3 py-1 rounded-3 shadow-sm"
                                    style="background-color: white; transition: background-color 0.3s ease; color: black;"
                                    id="toggle-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-sun" viewBox="0 0 16 16">
                                        <path d="M8 4.5a3.5 3.5 0 1 1 0 7a3.5 3.5 0 0 1 0-7zM8 0a.5.5 0 0 1 .5.5V2A.5.5 0 0 1 8 2V.5A.5.5 0 0 1 8 0zM8 14a.5.5 0 0 1 .5.5V16a.5.5 0 0 1-1 0v-1.5A.5.5 0 0 1 8 14zM2.343 2.343a.5.5 0 0 1 .707 0l1.06 1.06a.5.5 0 1 1-.707.708L2.343 3.05a.5.5 0 0 1 0-.707zm9.192 9.192a.5.5 0 0 1 .707 0l1.06 1.06a.5.5 0 0 1-.707.708l-1.06-1.06a.5.5 0 0 1 0-.707zM0 8a.5.5 0 0 1 .5-.5H2a.5.5 0 0 1 0 1H.5A.5.5 0 0 1 0 8zm14 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H14.5a.5.5 0 0 1-.5-.5zM2.343 13.657a.5.5 0 0 1 .707 0l1.06-1.06a.5.5 0 0 1-.707-.708l-1.06 1.06a.5.5 0 0 1 0 .708zm9.192-9.192a.5.5 0 0 1 .707 0l1.06-1.06a.5.5 0 0 1-.707-.708l-1.06 1.06a.5.5 0 0 1 0 .708z"/>
                                    </svg>
                                    <div class="form-check form-switch mx-2 mb-0">
                                        <input class="form-check-input" type="checkbox" id="toggle-dark" style="cursor: pointer;">
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-moon" viewBox="0 0 16 16">
                                        <path d="M6 0a7 7 0 0 0 0 14a7.001 7.001 0 0 0 6.938-6H13a6 6 0 0 1-11.855-1.156C.322 6.57 0 5.814 0 5a6 6 0 0 1 6-5z"/>
                                    </svg>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>

            </header>

            <!-- Loader Khusus Konten -->
            <div id="content-loader" style="display: flex; justify-content: center; align-items: center; height: 200px;">
                <div class="spinner"></div>
            </div>

            <!-- Konten Sebenarnya -->
            <div class="main-content container" style="display: none; font-size: 14px;">
                @yield('content')
            </div>

            <footer>
                <div class="container">
                    <div class="footer clearfix mb-0 text-muted">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center">
                            <div class="float-start">
                                <p>2024 &copy; SDN Sukarame</p>
                            </div>
                            <div class="float-end">
                                <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a
                                    href="https://github.com/anggarivian">Angga</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
    <script src="{{ asset('/dist/assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('/dist/assets/static/js/pages/horizontal-layout.js') }}"></script>

    <script src="{{ asset('/dist/assets/compiled/js/app.js') }}"></script>

    @yield('js')

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
        document.addEventListener("DOMContentLoaded", function () {
            const loader = document.getElementById("content-loader");
            const content = document.querySelector(".main-content");

            // Delay biar kerasa loading-nya (misal 700ms)
            setTimeout(() => {
                loader.style.display = "none";
                content.style.display = "block";

                // Aktifkan AOS kalau ada
                if (typeof AOS !== 'undefined') {
                    AOS.init();
                }
            }, 700);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const burgerBtn = document.querySelector('.burger-btn');
            const mainNavbar = document.querySelector('.main-navbar');

            burgerBtn.addEventListener('click', function (e) {
                e.preventDefault();
                mainNavbar.classList.toggle('show');
            });

            // Optional: klik di luar buat nutup
            document.addEventListener('click', function (e) {
                if (!mainNavbar.contains(e.target) && !burgerBtn.contains(e.target)) {
                    mainNavbar.classList.remove('show');
                }
            });
        });
    </script>

    <script>
        const toggle = document.getElementById('toggle-dark');
        const wrapper = document.getElementById('toggle-wrapper');

        toggle.addEventListener('change', function () {
            if (this.checked) {
                document.body.classList.add('dark-mode');
                wrapper.style.backgroundColor = '#2c2f33';
                wrapper.style.color = '#ffffff';
            } else {
                document.body.classList.remove('dark-mode');
                wrapper.style.backgroundColor = 'white';
                wrapper.style.color = 'black';
            }
        });
    </script>

    <script>
        document.getElementById('toggle-dark').addEventListener('change', function () {
            var logoImg = document.getElementById('logo-img');
            if (this.checked) {
                logoImg.src = "{{ asset('/dist/assets/compiled/svg/Logo Light.svg') }}";
            } else {
                logoImg.src = "{{ asset('/dist/assets/compiled/svg/Logo Dark.svg') }}";
            }
        });
    </script>

    <script>
        let deferredPrompt;
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

        window.addEventListener("beforeinstallprompt", (event) => {
            event.preventDefault();
            deferredPrompt = event;
            localStorage.setItem("pwaPromptAvailable", "true");
        });

        function showInstallPopup() {
            if (!localStorage.getItem("pwaPopupShown")) {
                let overlay = document.createElement("div");
                overlay.id = "installOverlay";
                overlay.style = `
                    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                    background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center;
                    z-index: 9999; opacity: 0; transition: opacity 0.3s ease-in-out;
                `;

                let installPopup = document.createElement("div");
                installPopup.id = "installBanner";
                installPopup.style = `
                    background: white; padding: 20px; border-radius: 12px; text-align: center;
                    width: 90%; max-width: 350px; box-shadow: 0px 4px 15px rgba(0,0,0,0.3);
                    transform: translateY(30px); opacity: 0; transition: all 0.3s ease-out;
                `;

                if (isSafari) {
                    // Tampilkan instruksi manual untuk Safari
                    installPopup.innerHTML = `
                        <p style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">Pasang Aplikasi SakuRame</p>
                        <p style="font-size: 14px; color: #555;">
                            Untuk menginstal aplikasi, tekan tombol <strong>Bagikan</strong> (ikon kotak dengan panah) di bawah, lalu pilih <strong>Tambahkan ke Layar Utama</strong>.
                        </p>
                        <button id="closeBtn" style="
                            width: 100%; padding: 10px; margin-top: 10px; background: #ccc; color: black;
                            border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
                            Tutup
                        </button>
                    `;
                } else {
                    // Popup normal untuk Chrome, Edge, dll.
                    installPopup.innerHTML = `
                        <p style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">Pasang Aplikasi SakuRame</p>
                        <p style="font-size: 14px; color: #555;">Instal aplikasi untuk kemudahan akses tanpa perlu browser.</p>
                        <button id="installBtn" style="
                            width: 100%; padding: 10px; margin-top: 10px; background: #007bff; color: white;
                            border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
                            Pasang Aplikasi
                        </button>
                        <button id="closeBtn" style="
                            width: 100%; padding: 10px; margin-top: 5px; background: #ccc; color: black;
                            border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
                            Tutup
                        </button>
                    `;
                }

                overlay.appendChild(installPopup);
                document.body.appendChild(overlay);

                // Animasi Fade In
                setTimeout(() => {
                    overlay.style.opacity = "1";
                    installPopup.style.transform = "translateY(0)";
                    installPopup.style.opacity = "1";
                }, 50);

                if (!isSafari) {
                    document.getElementById("installBtn").addEventListener("click", () => {
                        if (deferredPrompt) {
                            deferredPrompt.prompt();
                            deferredPrompt.userChoice.then((choice) => {
                                if (choice.outcome === "accepted") {
                                    console.log("User accepted PWA installation");
                                    localStorage.setItem("pwaPromptAvailable", "false");
                                } else {
                                    console.log("User dismissed PWA installation");
                                }
                                deferredPrompt = null;
                            });
                        }
                    });
                }

                document.getElementById("closeBtn").addEventListener("click", () => {
                    overlay.style.opacity = "0";
                    installPopup.style.transform = "translateY(30px)";
                    installPopup.style.opacity = "0";

                    setTimeout(() => {
                        overlay.remove();
                    }, 300);
                });

                localStorage.setItem("pwaPopupShown", "true");
            }
        }

        window.addEventListener("load", () => {
            if (isSafari || (localStorage.getItem("pwaPromptAvailable") === "true" && !localStorage.getItem("pwaPopupShown"))) {
                showInstallPopup();
            }
        });

    </script>


</body>

</html>
