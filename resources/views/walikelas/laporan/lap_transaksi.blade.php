@extends('layout.main')

@section('title', 'Laporan Transaksi - SakuRame')

@section('content')

{{-- Custom CSS Dashboard Style --}}
<style>
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
    }

    .icon-box svg {
        width: 28px;
        height: 28px;
    }

    .bg-blue-light { background-color: #e0f2fe; color: #0284c7; }
    .bg-green-light { background-color: #dcfce7; color: #16a34a; }
    .bg-red-light { background-color: #fee2e2; color: #dc2626; }
</style>

<div class="page-heading mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h4>Rekapitulasi Keuangan</h4>
            <h3>Laporan Transaksi</h3>
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
                    <li class="breadcrumb-item active" aria-current="page">Laporan Transaksi</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-blue-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2 2a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V2zm2.5 2.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0 3a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zm0 3a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold">Total Transaksi</h6>
                        <h5 class="fw-bolder mb-0">{{ number_format($totalTransaksi, 0, ',', '.') }} <span class="fs-6 text-muted fw-normal">Transaksi</span></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-green-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm.5-10.5v5.6l1.9-1.9a.5.5 0 1 1 .7.7l-2.75 2.75a.5.5 0 0 1-.7 0L4.9 8.9a.5.5 0 1 1 .7-.7l1.9 1.9V4.5a.5.5 0 0 1 1 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold">Total Stor</h6>
                        <h5 class="fw-bolder mb-0">Rp {{ number_format($totalStor, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-red-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 1a7 7 0 1 1 0 14A7 7 0 0 1 8 1zm.5 10.5V5.9l1.9 1.9a.5.5 0 0 0 .7-.7L8.35 4.35a.5.5 0 0 0-.7 0L4.9 7.1a.5.5 0 1 0 .7.7l1.9-1.9v5.6a.5.5 0 0 0 1 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold">Total Tarik</h6>
                        <h5 class="fw-bolder mb-0">Rp {{ number_format($totalTarik, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $filterAktif = request()->anyFilled([
            'rombel',
            'tipe_transaksi',
            'tipe_pembayaran',
            'search',
            'start_date',
            'end_date',
            'sort_date',
        ]) || (
            request('periode_id') &&
            request('periode_id') != \App\Helpers\PeriodeHelper::id()
        );
    @endphp

    {{-- Main Filter Panel --}}
    <div class="card stat-card shadow-sm">
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
                                        {{ $p->nama ?? 'Periode '.$p->id }} {{ $p->status == 'aktif' ? '★ Aktif' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Rombel --}}
                        <div class="col-12 col-md-3">
                            <label class="form-label small text-muted fw-medium mb-1">Rombel</label>
                            <select class="form-select" name="rombel" id="rombel">
                                <option value="">Semua Rombel</option>
                                @foreach ($rombelList as $r)
                                    <option value="{{ $r->id }}" {{ (string)request('rombel') === (string)$r->id ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Tipe Transaksi --}}
                        <div class="col-12 col-md-2">
                            <label class="form-label small text-muted fw-medium mb-1">Transaksi</label>
                            <select class="form-select" name="tipe_transaksi" id="tipe_transaksi">
                                <option value="">Semua</option>
                                <option value="Stor" {{ request('tipe_transaksi') == 'Stor' ? 'selected' : '' }}>Stor</option>
                                <option value="Tarik" {{ request('tipe_transaksi') == 'Tarik' ? 'selected' : '' }}>Tarik</option>
                            </select>
                        </div>

                        {{-- Filter Metode Pembayaran --}}
                        <div class="col-12 col-md-2">
                            <label class="form-label small text-muted fw-medium mb-1">Pembayaran</label>
                            <select class="form-select" name="tipe_pembayaran" id="tipe_pembayaran">
                                <option value="">Semua</option>
                                <option value="Tunai" {{ request('tipe_pembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                <option value="Digital" {{ request('tipe_pembayaran') == 'Digital' ? 'selected' : '' }}>Digital</option>
                            </select>
                        </div>

                        {{-- Sorting Urutan Tanggal --}}
                        <div class="col-12 col-md-2">
                            <label class="form-label small text-muted fw-medium mb-1">Urutan</label>
                            <select class="form-select" name="sort_date" id="sort_date">
                                <option value="desc" {{ request('sort_date','desc')=='desc'?'selected':'' }}>Terbaru</option>
                                <option value="asc" {{ request('sort_date')=='asc'?'selected':'' }}>Terlama</option>
                            </select>
                        </div>

                        {{-- Rentang Tanggal --}}
                        <div class="col-12 col-md-3">
                            <label class="form-label small text-muted fw-medium mb-1">Dari Tanggal</label>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label small text-muted fw-medium mb-1">Sampai Tanggal</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                        </div>

                        {{-- Input Pencarian --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted fw-medium mb-1">Pencarian</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama / username...">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                                <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="perPage" value="{{ request('perPage',10) }}">
                </form>

                {{-- Badges Filter Aktif --}}
                @if ($filterAktif)
                    <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                        <span class="small text-muted">Filter Aktif :</span>

                        @if (request('periode_id') && request('periode_id') != \App\Helpers\PeriodeHelper::id())
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock-history me-1"></i>History Periode
                            </span>
                        @endif

                        @if (request('rombel'))
                            <span class="badge bg-secondary">
                                Rombel: {{ $rombelList->firstWhere('id', request('rombel'))->name ?? '-' }}
                            </span>
                        @endif

                        @if (request('tipe_transaksi'))
                            <span class="badge border text-muted">Transaksi: {{ request('tipe_transaksi') }}</span>
                        @endif

                        @if (request('tipe_pembayaran'))
                            <span class="badge border text-muted">Pembayaran: {{ request('tipe_pembayaran') }}</span>
                        @endif

                        @if (request('sort_date'))
                            <span class="badge border text-muted">Urutan: {{ request('sort_date') == 'asc' ? 'Terlama' : 'Terbaru' }}</span>
                        @endif

                        @if (request('start_date'))
                            <span class="badge border text-muted">Dari: {{ request('start_date') }}</span>
                        @endif

                        @if (request('end_date'))
                            <span class="badge border text-muted">Sampai: {{ request('end_date') }}</span>
                        @endif

                        @if (request('search'))
                            <span class="badge border text-muted">Cari: "{{ request('search') }}"</span>
                        @endif

                        <a href="{{ url()->current() }}" class="badge bg-danger text-decoration-none">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Table Section --}}
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-medium">
                    Menampilkan {{ $transaksi->firstItem() ?? 0 }} – {{ $transaksi->lastItem() ?? 0 }} dari total {{ $transaksi->total() }} data
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center py-3" style="width:60px">No</th>
                            <th class="text-center py-3">Kode Rombel</th>
                            <th class="py-3">Nama Siswa</th>
                            <th class="text-center py-3">Rombel</th>
                            <th class="text-end py-3 pe-4">Saldo Awal</th>
                            <th class="text-end py-3 pe-4">Jumlah</th>
                            <th class="text-end py-3 pe-4">Saldo Akhir</th>
                            <th class="text-center py-3">Transaksi</th>
                            <th class="text-center py-3">Pembuat</th>
                            <th class="text-center py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $index => $transaksis)
                        <tr>
                            <td class="text-center">
                                {{ $transaksi->firstItem()+$index }}
                            </td>

                            {{-- KOLOM KODE ROMBEL: pakai relasi "rombel" (singular) sesuai eager load controller --}}
                            <td class="text-center">
                                <span class="badge bg-secondary">
                                    {{ $transaksis->user->rombel->first()->pivot->kode ?? '-' }}
                                </span>
                            </td>

                            <td class="fw-medium">
                                {{ Str::limit($transaksis->user->name, 25, '...') }}
                            </td>

                            {{-- KOLOM ROMBEL --}}
                            <td class="text-center">
                                @php
                                    $rombelName = $transaksis->user->rombel->first()->name ?? null;
                                @endphp
                                @if($rombelName)
                                    <span class="badge border text-muted">
                                        {{ $rombelName }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                Rp {{ number_format($transaksis->saldo_awal, 0, ',', '.') }}
                            </td>
                            <td class="text-end pe-4 fw-semibold">
                                Rp {{ number_format($transaksis->jumlah_transaksi, 0, ',', '.') }}
                            </td>
                            <td class="text-end pe-4 fw-bold">
                                Rp {{ number_format($transaksis->saldo_akhir, 0, ',', '.') }}
                            </td>

                            <td class="text-center">
                                @if(strtolower($transaksis->tipe_transaksi) == 'stor')
                                    <span class="badge bg-success">Stor | {{ $transaksis->pembayaran }}</span>
                                @else
                                    <span class="badge bg-danger">Tarik | {{ $transaksis->pembayaran }}</span>
                                @endif
                            </td>

                            <td class="text-center">
                                {{ Str::limit($transaksis->pembuat, 12, '...') }}
                            </td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($transaksis->created_at)->format('d M Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="bi bi-search display-6 d-block mb-3 text-secondary"></i>
                                Tidak ada data transaksi yang sesuai dengan filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                <form method="GET" action="{{ url()->current() }}" id="perPageForm">
                    @foreach(request()->except('perPage','page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="d-flex align-items-center gap-2">
                        <label for="perPage" class="small text-muted fw-medium mb-0">Show</label>
                        <select name="perPage" id="perPage" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            @foreach([10,25,50,100] as $opt)
                                <option value="{{ $opt }}" {{ (int)request('perPage',10) === $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div>
                    {{ $transaksi->links('layout.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const collapse = document.getElementById('filterCollapse');
    const icon = document.getElementById('filterIcon');
    const text = document.getElementById('filterText');

    collapse.addEventListener('shown.bs.collapse', function(){
        icon.classList.remove('bi-chevron-down');
        icon.classList.add('bi-chevron-up');
        text.innerHTML = 'Sembunyikan';
    });

    collapse.addEventListener('hidden.bs.collapse', function(){
        icon.classList.remove('bi-chevron-up');
        icon.classList.add('bi-chevron-down');
        text.innerHTML = 'Tampilkan';
    });
});
</script>
@endsection