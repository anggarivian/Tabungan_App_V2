@extends('layout.main')

@section('title', 'Laporan Tabungan - SakuRame')

@section('content')

{{-- Custom CSS Dashboard Style --}}
<style>
    /* Styling Animasi Teks Header */
    .page-heading h3 {
        font-size: 1.8rem;
        background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: gradientMove 3s ease-in-out infinite;
        font-weight: 800;
        margin-bottom: 0.2rem;
    }

    .page-heading h4 {
        font-size: 1.1rem;
        color: #6c757d;
        font-weight: 400;
        margin-bottom: 0;
    }

    @keyframes gradientMove {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* Styling Custom Card & Hover Effect */
    .stat-card {
        border: none;
        border-radius: 1rem;
        transition: all 0.3s ease;
        /* Hapus background: #ffffff; agar support Dark Mode */
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }

    .icon-box {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-box svg {
        width: 28px;
        height: 28px;
    }

    /* Custom Colors for Icons */
    .bg-blue-light { background-color: #e0f2fe; color: #0284c7; }
    .bg-green-light { background-color: #dcfce7; color: #16a34a; }
</style>

<div class="page-heading mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h4>Rekapitulasi Keuangan</h4>
            <h3>Laporan Tabungan</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    @if(auth()->user()->roles_id == 1)
                        <li class="breadcrumb-item"><a href="{{ route('kepsek.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 2)
                        <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 3)
                        <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Laporan Tabungan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    
    {{-- 1. BAGIAN ATAS: Card Summary Total (Desain Baru) --}}
    <div class="row g-3 mb-4">
        {{-- Card Total Siswa --}}
        <div class="col-12 col-md-6 col-xl-6">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-blue-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Total Siswa Terfilter</h6>
                        <h5 class="fw-bolder mb-0">{{ number_format($totalSiswa) }} <span class="fs-6 text-muted fw-normal">Orang</span></h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Total Saldo --}}
        <div class="col-12 col-md-6 col-xl-6">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-green-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/><path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/></svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Total Saldo Terfilter</h6>
                        <h5 class="fw-bolder mb-0">Rp {{ number_format($totalSaldoKeseluruhan, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $filterAktif = request()->anyFilled([
            'search',
            'rombel',
            'sort_saldo',
        ]) || (
            request('periode_id') &&
            request('periode_id') != \App\Helpers\PeriodeHelper::id()
        );
    @endphp

    {{-- 2. BAGIAN BAWAH: Card Utama Laporan --}}
    <div class="card stat-card shadow-sm">
        
        <div class=" mb-0">

            {{-- Header Filter --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-funnel me-2"></i>Filter Laporan
                </h6>

                <button class="btn btn-sm btn-outline-primary"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#filterCollapse"
                        aria-expanded="{{ $filterAktif ? 'true' : 'false' }}"
                        aria-controls="filterCollapse"
                        id="btnFilter">

                    <i class="bi {{ $filterAktif ? 'bi-chevron-up' : 'bi-chevron-down' }} me-1"
                    id="filterIcon"></i>

                    <span id="filterText">
                        {{ $filterAktif ? 'Sembunyikan' : 'Tampilkan' }}
                    </span>

                </button>
            </div>

            {{-- Isi Filter --}}
            <div class="collapse {{ $filterAktif ? 'show' : '' }}" id="filterCollapse">
                <div class="card-body p-4 border-top">

                    <form action="{{ url()->current() }}" method="GET" id="filterForm">

                        <div class="row g-3 align-items-end">

                            {{-- Filter Periode --}}
                            <div class="col-12 col-md-3">
                                <label class="form-label small text-muted fw-medium mb-1">Pilih Periode</label>
                                <select class="form-select" name="periode_id" id="periode_id" onchange="this.form.submit()">
                                    @foreach ($periodeList as $p)
                                        <option value="{{ $p->id }}" {{ (string)$selectedPeriodeId === (string)$p->id ? 'selected' : '' }}>
                                            {{ $p->nama ?? 'Periode '.$p->id }} {{ $p->status ? '★ Aktif' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Rombel --}}
                            <div class="col-12 col-md-3">
                                <label class="form-label small text-muted fw-medium mb-1">
                                    Rombel
                                </label>

                                <select class="form-select"
                                        name="rombel"
                                        id="rombel">

                                    <option value="">Semua Rombel</option>

                                    @foreach ($rombelList as $r)
                                        <option value="{{ $r->id }}"
                                            {{ (string) request('rombel') === (string) $r->id ? 'selected' : '' }}>
                                            {{ $r->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- Urutan Saldo --}}
                            <div class="col-12 col-md-2">
                                <label class="form-label small text-muted fw-medium mb-1">
                                    Urutan Saldo
                                </label>

                                <select class="form-select"
                                        name="sort_saldo"
                                        id="sort_saldo">

                                    <option value="">A-Z (Nama)</option>

                                    <option value="desc"
                                        {{ request('sort_saldo') == 'desc' ? 'selected' : '' }}>
                                        Terbesar
                                    </option>

                                    <option value="asc"
                                        {{ request('sort_saldo') == 'asc' ? 'selected' : '' }}>
                                        Terkecil
                                    </option>

                                </select>
                            </div>

                            {{-- Search --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label small text-muted fw-medium mb-1">
                                    Pencarian
                                </label>

                                <div class="input-group">

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="search"
                                        id="search"
                                        value="{{ request('search') }}"
                                        placeholder="Cari nama / kode...">

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>

                                    <a href="{{ url()->current() }}"
                                    class="btn btn-outline-secondary"
                                    title="Reset">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </a>

                                </div>
                            </div>

                        </div>

                    </form>

                    {{-- Badge Filter Aktif --}}
                    @if ($filterAktif)
                        <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">

                            <span class="small text-muted">
                                Filter Aktif :
                            </span>

                            @if (request('periode_id') && request('periode_id') != \App\Helpers\PeriodeHelper::id())
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock-history me-1"></i>
                                    History Periode
                                </span>
                            @endif

                            @if (request('rombel'))
                                <span class="badge bg-secondary">
                                    Rombel :
                                    {{ $rombelList->firstWhere('id', request('rombel'))->name ?? '-' }}
                                </span>
                            @endif

                            @if (request('search'))
                                <span class="badge border text-muted">
                                    Cari :
                                    "{{ request('search') }}"
                                </span>
                            @endif

                            @if (request('sort_saldo'))
                                <span class="badge border text-muted">
                                    Urut :
                                    {{ request('sort_saldo') == 'desc' ? 'Terbesar' : 'Terkecil' }}
                                </span>
                            @endif

                            <a href="{{ url()->current() }}" class="badge bg-danger text-decoration-none">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>

                        </div>
                    @endif

                </div>
            </div>

        </div>

        {{-- Section Tabel --}}
        <div class="card-body p-4">
            
            {{-- Info Pagination di Atas Tabel --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-medium">
                    Menampilkan {{ $user->firstItem() ?? 0 }}–{{ $user->lastItem() ?? 0 }} dari total {{ $user->total() }} data
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center py-3" style="width: 60px">No</th>
                            <th class="text-center py-3" style="width: 120px">Kode</th>
                            <th class="py-3">Nama Siswa</th>
                            <th class="text-center py-3">Rombel</th>
                            <th class="text-end py-3 pe-4">Total Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user as $index => $users)
                            @php 
                                $kodeUnik = $users->rombel->first()->pivot->kode ?? $users->username; 
                            @endphp
                            <tr>
                                <td class="text-center">{{ $user->firstItem() + $index }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $kodeUnik }}</span>
                                </td>
                                <td class="fw-medium">{{ $users->name }}</td>
                                <td class="text-center">
                                    <span class="badge border text-muted">
                                        {{ $users->rombel->first()->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold pe-4">
                                    Rp {{ number_format($users->tabungan->saldo ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-search display-6 d-block mb-3 text-secondary"></i>
                                    Tidak ada data tabungan yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination (Show Dropdown Angka Saja) --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                <form method="GET" action="{{ url()->current() }}" id="perPageForm">
                    @foreach (request()->except('perPage', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <div class="d-flex align-items-center gap-2">
                        <label for="perPage" class="small text-muted fw-medium mb-0">Show</label>
                        <select name="perPage" id="perPage" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            @foreach ([10, 25, 50, 100] as $opt)
                                {{-- Option HANYA nampilin angka --}}
                                <option value="{{ $opt }}" {{ (int) request('perPage', 10) === $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div>
                    {{ $user->links('layout.pagination.bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const collapse = document.getElementById('filterCollapse');
    const icon = document.getElementById('filterIcon');
    const text = document.getElementById('filterText');

    collapse.addEventListener('shown.bs.collapse', function () {
        icon.classList.remove('bi-chevron-down');
        icon.classList.add('bi-chevron-up');
        text.innerHTML = 'Sembunyikan';
    });

    collapse.addEventListener('hidden.bs.collapse', function () {
        icon.classList.remove('bi-chevron-up');
        icon.classList.add('bi-chevron-down');
        text.innerHTML = 'Tampilkan';
    });

});
</script>
@endsection