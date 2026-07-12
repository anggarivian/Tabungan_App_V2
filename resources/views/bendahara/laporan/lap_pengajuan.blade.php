@extends('layout.main')

@section('title', 'Laporan Pengajuan - SakuRame')

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
        flex-shrink: 0;
    }

    .icon-box i {
        font-size: 1.4rem;
    }

    /* Custom Colors for Icons */
    .bg-blue-light   { background-color: #e0f2fe; color: #0284c7; }
    .bg-green-light  { background-color: #dcfce7; color: #16a34a; }
    .bg-orange-light { background-color: #ffedd5; color: #ea580c; }
    .bg-red-light    { background-color: #fee2e2; color: #dc2626; }

    /* Filter row: rata kiri-kanan, tiap field tumbuh merata mengisi lebar */
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .filter-row > .filter-item {
        flex: 1 1 150px;
    }

    .filter-row > .filter-item.filter-search {
        flex: 2 1 240px;
    }
</style>

<div class="page-heading mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h4>Rekapitulasi Pengajuan</h4>
            <h3>Laporan Pengajuan</h3>
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
                    @elseif(auth()->user()->roles_id == 4)
                        <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Laporan Pengajuan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">

    {{-- 1. BAGIAN ATAS: Card Summary Total --}}
    <div class="row g-3 mb-4">

        {{-- Total Pengajuan --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-blue-light me-3">
                        <i class="bi bi-file-earmark-text" style="margin-top: -20px; margin-left: -5px;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Total Pengajuan</h6>
                        <h5 class="fw-bolder mb-0">{{ number_format($totalPengajuan) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-orange-light me-3">
                        <i class="bi bi-hourglass-split" style="margin-top: -20px; margin-left: -5px;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Pending</h6>
                        <h5 class="fw-bolder mb-0">{{ number_format($totalPending) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Diterima --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-green-light me-3">
                        <i class="bi bi-check-circle" style="margin-top: -20px; margin-left: -5px;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Diterima</h6>
                        <h5 class="fw-bolder mb-0">{{ number_format($totalDiterima) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ditolak --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-red-light me-3">
                        <i class="bi bi-x-circle" style="margin-top: -20px; margin-left: -5px;"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Ditolak</h6>
                        <h5 class="fw-bolder mb-0">{{ number_format($totalDitolak) }}</h5>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @php
        $filterAktif = request()->anyFilled(['search', 'rombel', 'status', 'sort_penarikan', 'start_date', 'end_date']) || request('periode_id');
    @endphp

    {{-- 2. BAGIAN BAWAH: Card Utama Laporan --}}
    <div class="card stat-card shadow-sm">

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
                <i class="bi {{ $filterAktif ? 'bi-chevron-up' : 'bi-chevron-down' }} me-1" id="filterIcon"></i>
                <span id="filterText">{{ $filterAktif ? 'Sembunyikan' : 'Tampilkan' }}</span>
            </button>
        </div>

        {{-- Isi Filter --}}
        <div class="collapse {{ $filterAktif ? 'show' : '' }}" id="filterCollapse">
            <div class="card-body p-4 border-top">

                <form action="{{ url()->current() }}" method="GET" id="filterForm">

                    <div class="filter-row">

                        {{-- Filter Periode --}}
                            <div class="col-12 col-md-3">
                                <label class="form-label small text-muted fw-medium mb-1">Pilih Periode</label>
                                <select class="form-select" name="periode_id" id="periode_id" onchange="this.form.submit()">
                                    @foreach ($periodeList as $p)
                                        <option value="{{ $p->id }}" {{ (string)$periodeId === (string)$p->id ? 'selected' : '' }}>
                                            {{ $p->nama ?? 'Periode '.$p->id }} {{ $p->status ? '★ Aktif' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        {{-- Filter Rombel (sesuai periode terpilih) --}}
                        <div class="filter-item">
                            <label class="form-label small text-muted fw-medium mb-1">
                                Rombel
                            </label>
                            <select class="form-select" name="rombel" id="rombel">
                                <option value="">Semua Rombel</option>
                                @foreach ($rombelList as $r)
                                    <option value="{{ $r->id }}"
                                        {{ (string) request('rombel') === (string) $r->id ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="filter-item">
                            <label for="status" class="form-label small text-muted fw-medium mb-1">
                                Status
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Semua Status</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Terima" {{ request('status') == 'Terima' ? 'selected' : '' }}>Diterima</option>
                                <option value="Tolak" {{ request('status') == 'Tolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        {{-- Urutan Tanggal --}}
                        <div class="filter-item">
                            <label for="sort_date" class="form-label small text-muted fw-medium mb-1">
                                Urutkan Tanggal
                            </label>
                            <select class="form-select" name="sort_date" id="sort_date">
                                <option value="">Default</option>
                                <option value="desc" {{ request('sort_date') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                                <option value="asc" {{ request('sort_date') == 'asc' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>

                        {{-- Urutan Besaran --}}
                        <div class="filter-item">
                            <label for="sort_penarikan" class="form-label small text-muted fw-medium mb-1">
                                Urutkan Besaran
                            </label>
                            <select class="form-select" name="sort_penarikan" id="sort_penarikan">
                                <option value="">Default</option>
                                <option value="desc" {{ request('sort_penarikan') == 'desc' ? 'selected' : '' }}>Terbesar</option>
                                <option value="asc" {{ request('sort_penarikan') == 'asc' ? 'selected' : '' }}>Terkecil</option>
                            </select>
                        </div>

                        {{-- Filter Tanggal + Search --}}
                        <div class="filter-item" style="flex:3 1 600px;">
                            <label class="form-label small text-muted fw-medium mb-1">
                                Pencarian & Rentang Tanggal
                            </label>

                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input
                                        type="date"
                                        class="form-control"
                                        name="start_date"
                                        id="start_date"
                                        value="{{ request('start_date') }}">
                                </div>

                                <div class="col-md-3">
                                    <input
                                        type="date"
                                        class="form-control"
                                        name="end_date"
                                        id="end_date"
                                        value="{{ request('end_date') }}">
                                </div>

                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="search"
                                            id="search"
                                            placeholder="Cari Nama / Username..."
                                            value="{{ request('search') }}">

                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search"></i>
                                        </button>

                                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
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

                        @if (request('status'))
                            <span class="badge border text-muted">
                                Status : {{ request('status') }}
                            </span>
                        @endif

                        @if (request('search'))
                            <span class="badge border text-muted">
                                Cari : "{{ request('search') }}"
                            </span>
                        @endif

                        @if (request('sort_date'))
                            <span class="badge border text-muted">
                                Tanggal : {{ request('sort_date') == 'desc' ? 'Terbaru' : 'Terlama' }}
                            </span>
                        @endif

                        @if (request('sort_penarikan'))
                            <span class="badge border text-muted">
                                Besaran : {{ request('sort_penarikan') == 'desc' ? 'Terbesar' : 'Terkecil' }}
                            </span>
                        @endif

                        @if (request('start_date'))
                            <span class="badge border text-muted">
                                Dari : {{ request('start_date') }}
                            </span>
                        @endif

                        @if (request('end_date'))
                            <span class="badge border text-muted">
                                Sampai : {{ request('end_date') }}
                            </span>
                        @endif

                        <a href="{{ url()->current() }}" class="badge bg-danger text-decoration-none">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>

                    </div>
                @endif

            </div>
        </div>

        {{-- Section Tabel --}}
        <div class="card-body p-4">

            {{-- Info Pagination di Atas Tabel --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-medium">
                    Menampilkan {{ $pengajuan->firstItem() ?? 0 }}–{{ $pengajuan->lastItem() ?? 0 }} dari total {{ $pengajuan->total() }} pengajuan
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center py-3" style="width: 60px">No</th>
                            <th class="text-center py-3">Username</th>
                            <th class="py-3">Nama Siswa</th>
                            <th class="text-center py-3">Rombel</th>
                            <th class="text-end py-3">Saldo</th>
                            <th class="text-end py-3">Jumlah Penarikan</th>
                            <th class="text-center py-3">Status</th>
                            <th class="py-3">Alasan</th>
                            <th class="text-center py-3 pe-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan as $index => $pengajuans)
                            <tr>
                                <td class="text-center">{{ $pengajuan->firstItem() + $index }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $pengajuans->user->username }}</span>
                                </td>
                                <td class="fw-medium">{{ Str::limit($pengajuans->user->name, 20, '...') }}</td>
                                <td class="text-center">
                                    <span class="badge border text-muted">
                                        {{ $pengajuans->user->rombels->first()->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-end">Rp {{ number_format($pengajuans->user?->tabungan?->first()?->saldo ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">Rp {{ number_format($pengajuans->jumlah_penarikan ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if (in_array($pengajuans->status, ['Pending', 'pending']))
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif (in_array($pengajuans->status, ['Batal', 'batal']))
                                        <span class="badge bg-danger">Batal</span>
                                    @elseif (in_array($pengajuans->status, ['Tolak', 'tolak']))
                                        <span class="badge bg-danger">Tolak</span>
                                    @elseif (in_array($pengajuans->status, ['Terima', 'Diterima', 'SUCCEEDED']))
                                        <span class="badge bg-success">Terima</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                    <span class="badge border text-muted">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                </td>
                                <td>{{ Str::limit($pengajuans->alasan, 20, '...') }}</td>
                                <td class="text-center pe-4">{{ \Carbon\Carbon::parse($pengajuans->created_at)->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-6 d-block mb-3 text-secondary"></i>
                                    Tidak ada data pengajuan yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                <form method="GET" action="{{ url()->current() }}" id="perPageForm">
                    @foreach (request()->except('perPage', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <div class="d-flex align-items-center gap-2">
                        <label for="perPage" class="small text-muted fw-medium mb-0">Show</label>
                        <select name="perPage" id="perPage" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            @foreach ([10, 25, 50, 75, 100] as $opt)
                                <option value="{{ $opt }}" {{ (int) request('perPage', 10) === $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div>
                    {{ $pengajuan->links('layout.pagination.bootstrap-5') }}
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