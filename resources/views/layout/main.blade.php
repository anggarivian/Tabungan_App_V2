<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- PWA -->
    <meta name="theme-color" content="#6777ef"/>
    <link rel="apple-touch-icon" href="{{ asset('Logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <title>@yield('title')</title>
    {{-- @laravelPWA --}}
    @yield('style')

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

</head>


<body>
    <script src="{{ asset('/dist/assets/static/js/initTheme.js') }}"></script>
    <div id="loading-screen">
        <div class="spinner"></div>
    </div>
    <div id="app">
        <div id="main" class="layout-horizontal">
            <header class="mb-5">
                <div class="header-top">
                    <div class="container">
                        <a class="d-flex" href="{{ route(Auth::user()->roles_id == 1 ? 'kepsek.dashboard' : (Auth::user()->roles_id == 2 ? 'bendahara.dashboard' : (Auth::user()->roles_id == 3 ? 'walikelas.dashboard' : 'siswa.dashboard'))) }}">
                            <img id="logo-img" src="{{ asset('/dist/assets/compiled/svg/Logo Dark.svg') }}" height="40px" width="190px" alt="Logo">
                        </a>
                        <div class="header-top-right">
                            <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                                <div class="form-check form-switch fs-6">
                                    <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="#" id="topbarUserDropdown" class="user-dropdown d-flex align-items-center dropend dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="d-none d-md-flex">
                                        <div class="avatar avatar-md2">
                                            <img src="{{ asset('/dist/assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                        </div>
                                        <div class="text">
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
                                                {{ $roles[auth()->user()->roles_id] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-md-none">
                                        <div class="avatar avatar-md2">
                                            <img src="{{ asset('/dist/assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="topbarUserDropdown">
                                    <li><a class="dropdown-item" href="{{ route ('change.password') }}">Ganti Password</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            <!-- Burger button responsive -->
                            <a href="#" class="burger-btn d-block d-xl-none">
                                <i class="bi bi-justify fs-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <nav class="main-navbar">
                    <div class="container">
                        <ul>
                            {{-- Dasdhboard -------------------------------------------------------------------------------------------------------------------- --}}
                            @if( auth()->user()->roles_id == 1 )
                            <li class="menu-item {{ request()->routeIs('kepsek.dashboard') ? 'active' : '' }}">
                                <a href="{{ route ('kepsek.dashboard')}}" class='menu-link' style="margin-top: -3px">
                                    <span><i class="bi bi-house-fill"></i> Dashboard</span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 2)
                            <li class="menu-item {{ request()->routeIs('bendahara.dashboard') ? 'active' : '' }}">
                                <a href="{{ route ('bendahara.dashboard')}}" class='menu-link' style="margin-top: -3px">
                                    <span><i class="bi bi-house-fill"></i> Dashboard</span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 3)
                            <li class="menu-item {{ request()->routeIs('walikelas.dashboard') ? 'active' : '' }}">
                                <a href="{{ route ('walikelas.dashboard')}}" class='menu-link' style="margin-top: -3px">
                                    <span><i class="bi bi-house-fill"></i> Dashboard</span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 4)
                            <li class="menu-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                                <a href="{{ route ('siswa.dashboard')}}" class='menu-link' style="margin-top: -3px">
                                    <span><i class="bi bi-house-fill"></i> Dashboard</span>
                                </a>
                            </li>
                            @endif
                            {{-- Transaksi Tabungan -------------------------------------------------------------------------------------------------------------------- --}}
                            @if( auth()->user()->roles_id == 2 )
                            <li class="menu-item {{ request()->routeIs('tabungan.index') || request()->routeIs('tabungan.stor') || request()->routeIs('tabungan.tarik') ? 'active' : '' }}">
                                <a href="{{ route ('bendahara.tabungan.index')}}" class='menu-link' style="margin-top: -3px">
                                    <span><i class="bi bi-bank2"></i> Tabungan </span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 3)
                            <li class="menu-item {{ request()->routeIs('walikelas.tabungan.index') ? 'active' : '' }}">
                                <a href="{{ route ('walikelas.tabungan.index')}}" class='menu-link' style="margin-top: -3px">
                                    <span><i class="bi bi-bank2"></i> Stor Tabungan </span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 4)
                            <li class="menu-item {{ request()->routeIs('siswa.tabungan.stor') ? 'active' : '' }}">
                                <a href="{{ route ('siswa.tabungan.stor')}}" class='menu-link' style="margin-top: -3px">
                                    <span><i class="bi bi-bank2"></i> Stor </span>
                                </a>
                            </li>
                            @endif
                            {{-- Pengajuan -------------------------------------------------------------------------------------------------------------------- --}}
                            @if( auth()->user()->roles_id == 2 )
                            <li class="menu-item {{ request()->routeIs('pengajuan.index*') ? 'active' : '' }}">
                                <a href="{{ route ('bendahara.pengajuan.index')}}" class='menu-link' style="margin-top: -3px">
                                    <span><i class="bi bi-hourglass-split"></i> Pengajuan </span>
                                </a>
                            </li>
                            @elseif( auth()->user()->roles_id == 4)
                            <li class="menu-item {{ request()->routeIs('siswa.tabungan.tarik*') ? 'active' : '' }}">
                                <a href="{{ route ('siswa.tabungan.tarik')}}" class='menu-link' style="margin-top: -3px">
                                    <span><i class="bi bi-bank2"></i> Tarik </span>
                                </a>
                            </li>
                            @endif
                            {{-- Laporan -------------------------------------------------------------------------------------------------------------------- --}}
                            @if( auth()->user()->roles_id == 1)
                            <li class="menu-item {{ request()->is('kepsek/laporan*') ? 'active' : '' }} has-sub">
                                <a href="#" class="menu-link" style="margin-top: -3px">
                                    <span><i class="bi bi-book-fill"></i> Laporan</span>
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
                                <a href="#" class="menu-link" style="margin-top: -3px">
                                    <span><i class="bi bi-book-fill"></i> Laporan</span>
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
                                <a href="#" class="menu-link" style="margin-top: -3px">
                                    <span><i class="bi bi-book-fill"></i> Laporan</span>
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
                                <a href="#" class="menu-link" style="margin-top: -3px">
                                    <span><i class="bi bi-book-fill"></i> Laporan</span>
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
                                    <a href="#" class="menu-link" style="margin-top: -3px">
                                        <span><i class="bi bi-people-fill"></i> Data Pengguna</span>
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
                            {{-- <li class="menu-item {{ request()->is('profil-sekolah*') ? 'active' : '' }}">
                                <a href="#" class='menu-link' style="margin-top: -3px">
                                    <span><i class="bi bi-building-fill-gear"></i> Profil Sekolah</span>
                                </a>
                            </li> --}}
                        </ul>
                    </div>
                </nav>

            </header>

            <div class="content-wrapper container" id="content" style="display: none;">
                @yield('content')
            </div>

            <footer>
                <div class="container">
                    <div class="footer clearfix mb-0 text-muted">
                        <div class="float-start">
                            <p>2024 &copy; SDN Sukarame</p>
                        </div>
                        <div class="float-end">
                            <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a
                                href="https://github.com/anggarivian">Angga</a></p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
    <script src="{{ asset('/dist/assets/static/js/components/dark.js') }}"></script>
    <script src="{{ asset('/dist/assets/static/js/pages/horizontal-layout.js') }}"></script>
    <script src="{{ asset('/dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>

    <script src="{{ asset('/dist/assets/compiled/js/app.js') }}"></script>

    <script src="{{ asset('/dist/assets/static/js/pages/dashboard.js') }}"></script>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loading-screen').style.display = 'none';
                document.getElementById('content').style.display = 'block';
            }, 200);
        });
    </script>

    @yield('js')

    <script>
        document.getElementById('toggle-dark').addEventListener('change', function () {
            var logoImg = document.getElementById('logo-img');
            if (this.checked) {
                // Jika checkbox on
                logoImg.src = "{{ asset('/dist/assets/compiled/svg/Logo Light.svg') }}";
            } else {
                // Jika checkbox off
                logoImg.src = "{{ asset('/dist/assets/compiled/svg/Logo Dark.svg') }}";
            }
        });
    </script>

    {{-- <script src="{{ asset('/sw.js') }}"></script>
    <script>
    if ("serviceWorker" in navigator) {
        // Register a service worker hosted at the root of the
        // site using the default scope.
        navigator.serviceWorker.register("/sw.js").then(
        (registration) => {
            console.log("Service worker registration succeeded:", registration);
        },
        (error) => {
            console.error(`Service worker registration failed: ${error}`);
        },
        );
    } else {
        console.error("Service workers are not supported.");
    }
    </script> --}}

    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        if (!navigator.serviceWorker.controller) {
            navigator.serviceWorker.register("/sw.js").then(function (reg) {
            console.log("Service worker registered for scope: " + reg.scope);
            });
        }
    </script>


</body>

</html>
