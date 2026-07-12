@extends('layout.main')

@section('title') Laporan Pengajuan - SakuRame @endsection

@section('content')

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
    .bg-yellow-light { background-color: #fef9c3; color: #ca8a04; }
    .bg-green-light { background-color: #dcfce7; color: #16a34a; }
    .bg-red-light { background-color: #fee2e2; color: #dc2626; }
</style>

<div class="page-heading mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h4>Riwayat Penarikan Dana</h4>
            <h3>Laporan Pengajuan Saya</h3>
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

    {{-- Info rombel siswa di periode yang sedang dilihat --}}
    <div class="alert alert-light border d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-mortarboard-fill text-primary"></i>
        @if ($rombelInfo)
            Kamu tergabung di kelas <strong>{{ $rombelInfo->name }}</strong>
            dengan kode absen <strong>{{ $rombelInfo->pivot->kode }}</strong>
            pada periode ini.
        @else
            Kamu belum tergabung di rombel manapun pada periode yang dipilih.
        @endif
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-blue-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 3a2 2 0 0 1 2-2h13.5a.5.5 0 0 1 0 1H15v2a1 1 0 0 1 1 1v8.5a1.5 1.5 0 0 1-1.5 1.5h-11A2.5 2.5 0 0 1 1 12.5V4a1 1 0 0 1-1-1zm1 1v8.5A1.5 1.5 0 0 0 2.5 14h11a.5.5 0 0 0 .5-.5V5H1z"/>
                        </svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold">Total Pengajuan</h6>
                        <h5 class="fw-bolder mb-0">{{ number_format($totalPengajuan) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-yellow-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold">Pending</h6>
                        <h5 class="fw-bolder mb-0">{{ number_format($totalPending) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-green-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold">Diterima</h6>
                        <h5 class="fw-bolder mb-0">{{ number_format($totalDiterima) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-red-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fw-semibold">Ditolak</h6>
                        <h5 class="fw-bolder mb-0">{{ number_format($totalDitolak) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $filterAktif = request()->anyFilled(['status', 'start_date', 'end_date'])
            || ($selectedPeriodeId != \App\Helpers\PeriodeHelper::id());
    @endphp

    {{-- Card gabungan: Filter (header) + Tabel (body) --}}
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
                <i class="bi {{ $filterAktif ? 'bi-chevron-up' : 'bi-chevron-down' }} me-1" id="filterIcon"></i>
                <span id="filterText">{{ $filterAktif ? 'Sembunyikan' : 'Tampilkan' }}</span>
            </button>
        </div>

        <div class="collapse {{ $filterAktif ? 'show' : '' }}" id="filterCollapse">
            <div class="card-body p-4 border-bottom">
                <form action="{{ url()->current() }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">

                        <div class="col-12 col-md-3">
                            <label class="form-label small text-muted fw-medium mb-1">Pilih Periode</label>
                            <select class="form-select" name="periode_id" id="periode_id" onchange="this.form.submit()">
                                @foreach ($periodeList as $p)
                                    <option value="{{ $p->id }}" {{ (string) $selectedPeriodeId === (string) $p->id ? 'selected' : '' }}>
                                        {{ $p->nama ?? 'Periode '.$p->id }}
                                        {{ $p->id == \App\Helpers\PeriodeHelper::id() ? '★ Aktif' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small text-muted fw-medium mb-1">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Semua Status</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Terima" {{ request('status') == 'Terima' ? 'selected' : '' }}>Diterima</option>
                                <option value="Tolak" {{ request('status') == 'Tolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small text-muted fw-medium mb-1">Urutkan Besaran</label>
                            <select class="form-select" name="sort_penarikan" id="sort_penarikan">
                                <option value="">Default</option>
                                <option value="desc" {{ request('sort_penarikan') == 'desc' ? 'selected' : '' }}>Terbesar</option>
                                <option value="asc" {{ request('sort_penarikan') == 'asc' ? 'selected' : '' }}>Terkecil</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small text-muted fw-medium mb-1">Urutkan Tanggal</label>
                            <select class="form-select" name="sort_date" id="sort_date">
                                <option value="desc" {{ request('sort_date', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                                <option value="asc" {{ request('sort_date') == 'asc' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small text-muted fw-medium mb-1">Dari Tanggal</label>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small text-muted fw-medium mb-1">Sampai Tanggal</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                        </div>

                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary" title="Reset Filter">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                        </div>
                    </div>

                    <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
                </form>

                @if ($filterAktif)
                    <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                        <span class="small text-muted">Filter aktif:</span>

                        @if ($selectedPeriodeId != \App\Helpers\PeriodeHelper::id())
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock-history me-1"></i>Histori Periode
                            </span>
                        @endif

                        @if (request('status'))
                            <span class="badge border text-muted">Status: {{ request('status') }}</span>
                        @endif

                        @if (request('start_date'))
                            <span class="badge border text-muted">Dari: {{ request('start_date') }}</span>
                        @endif

                        @if (request('end_date'))
                            <span class="badge border text-muted">Sampai: {{ request('end_date') }}</span>
                        @endif

                        <a href="{{ url()->current() }}" class="badge bg-danger text-decoration-none">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tabel Pengajuan --}}
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small fw-medium">
                    Menampilkan {{ $pengajuan->firstItem() ?? 0 }} – {{ $pengajuan->lastItem() ?? 0 }} dari total {{ $pengajuan->total() }} data
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center py-3" style="width: 60px">No.</th>
                            <th class="text-end py-3 pe-4">Saldo Saat Itu</th>
                            <th class="text-end py-3 pe-4">Jumlah Penarikan</th>
                            <th class="text-center py-3">Status</th>
                            <th class="py-3">Alasan</th>
                            <th class="text-center py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan as $index => $pengajuans)
                            <tr>
                                <td class="text-center">{{ $pengajuan->firstItem() + $index }}</td>
                                <td class="text-end pe-4">Rp {{ number_format($pengajuans->user?->tabungan?->first()?->saldo ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end pe-4 fw-semibold">Rp {{ number_format($pengajuans->jumlah_penarikan ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if (in_array($pengajuans->status, ['Pending', 'pending']))
                                        <span class="badge bg-warning">Pending</span>
                                        <span class="badge bg-light text-dark">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @elseif (in_array($pengajuans->status, ['Batal', 'batal']))
                                        <span class="badge bg-danger">Batal</span>
                                        <span class="badge bg-light text-dark">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @elseif (in_array($pengajuans->status, ['Tolak', 'tolak']))
                                        <span class="badge bg-danger">Tolak</span>
                                        <span class="badge bg-light text-dark">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @elseif (in_array($pengajuans->status, ['Terima', 'Diterima', 'SUCCEEDED']))
                                        <span class="badge bg-success">Terima</span>
                                        <span class="badge bg-light text-dark">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($pengajuans->alasan, 30, '...') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($pengajuans->created_at)->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-6 d-block mb-3 text-secondary"></i>
                                    Belum ada pengajuan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4 pt-3 border-top">
                <span class="text-muted small">
                    Menampilkan {{ $pengajuan->firstItem() ?? 0 }}–{{ $pengajuan->lastItem() ?? 0 }}
                    dari {{ $pengajuan->total() }} pengajuan
                </span>

                <form method="GET" action="{{ url()->current() }}" id="perPageForm">
                    @foreach (request()->except(['perPage', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <div class="d-flex align-items-center gap-2">
                        <label for="perPage" class="small text-muted fw-medium mb-0">Show</label>
                        <select name="perPage" id="perPage" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            @foreach ([10, 25, 50, 100] as $opt)
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
    const filterCollapse = document.getElementById('filterCollapse');
    const toggleBtn = document.getElementById('btnFilter');
    const toggleText = document.getElementById('filterText');
    const toggleIcon = document.getElementById('filterIcon');

    function updateBtnState(isOpen) {
        toggleText.textContent = isOpen ? 'Sembunyikan' : 'Tampilkan';
        toggleIcon.classList.toggle('bi-chevron-down', !isOpen);
        toggleIcon.classList.toggle('bi-chevron-up', isOpen);
    }

    updateBtnState(filterCollapse.classList.contains('show'));

    filterCollapse.addEventListener('shown.bs.collapse', () => updateBtnState(true));
    filterCollapse.addEventListener('hidden.bs.collapse', () => updateBtnState(false));
});
</script>
@endsection