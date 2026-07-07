@extends('layout.main')

@section('title') Laporan Pengajuan - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Laporan Pengajuan</h3>
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
    <div class="card shadow mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-funnel"></i> Filter Laporan</h6>
                <button class="btn btn-sm btn-outline-primary" type="button"
                    data-bs-toggle="collapse" data-bs-target="#filterCollapse"
                    aria-expanded="false" aria-controls="filterCollapse"
                    id="filterToggleBtn">
                    <span class="toggle-text">Tampilkan Filter</span>
                    <i class="bi bi-chevron-down toggle-icon"></i>
                </button>
            </div>

            {{-- Default collapsed, otomatis kebuka kalau ada filter aktif --}}
            <div class="collapse {{ request()->anyFilled(['search', 'kelas', 'status', 'start_date', 'end_date']) ? 'show' : '' }} mt-3"
                id="filterCollapse">
                <form action="{{ url()->current() }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">

                        <div class="col-12 col-md-6 col-lg-3">
                            <label for="search" class="form-label small text-muted mb-1">Cari</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-search" style="margin-top:-10px"></i></span>
                                <input type="text" class="form-control" name="search" id="search"
                                    value="{{ request('search') }}" placeholder="Nama / Username">
                            </div>
                        </div>

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="kelas" class="form-label small text-muted mb-1">Kelas</label>
                            <select class="form-select" name="kelas" id="kelas">
                                <option value="">Semua Kelas</option>
                                @foreach ($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}"
                                        {{ (string) request('kelas') === (string) $kelas->id ? 'selected' : '' }}>
                                        {{ $kelas->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="status" class="form-label small text-muted mb-1">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Semua Status</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Terima" {{ request('status') == 'Terima' ? 'selected' : '' }}>Diterima</option>
                                <option value="Tolak" {{ request('status') == 'Tolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="sort_date" class="form-label small text-muted mb-1">Urutkan Tanggal</label>
                            <select class="form-select" name="sort_date" id="sort_date">
                                <option value="">Default</option>
                                <option value="desc" {{ request('sort_date') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                                <option value="asc" {{ request('sort_date') == 'asc' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="sort_penarikan" class="form-label small text-muted mb-1">Urutkan Besaran</label>
                            <select class="form-select" name="sort_penarikan" id="sort_penarikan">
                                <option value="">Default</option>
                                <option value="desc" {{ request('sort_penarikan') == 'desc' ? 'selected' : '' }}>Terbesar</option>
                                <option value="asc" {{ request('sort_penarikan') == 'asc' ? 'selected' : '' }}>Terkecil</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="start_date" class="form-label small text-muted mb-1">Dari Tanggal</label>
                            <input type="date" class="form-control" name="start_date" id="start_date"
                                value="{{ request('start_date') }}">
                        </div>

                        <div class="col-6 col-md-3 col-lg-2">
                            <label for="end_date" class="form-label small text-muted mb-1">Sampai Tanggal</label>
                            <input type="date" class="form-control" name="end_date" id="end_date"
                                value="{{ request('end_date') }}">
                        </div>

                        <div class="col-6 col-md-3 col-lg-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary" title="Reset Filter">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>

                    <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
                </form>
            </div>

            {{-- Badge filter aktif --}}
            @if (request()->anyFilled(['search', 'kelas', 'status', 'start_date', 'end_date']))
                <div class="mt-3 d-flex align-items-center flex-wrap gap-2">
                    <span class="text-muted small">Filter aktif:</span>

                    @if (request('search'))
                        <span class="badge bg-light text-dark border">Cari: "{{ request('search') }}"</span>
                    @endif

                    @if (request('kelas'))
                        <span class="badge bg-light text-dark border">
                            Kelas: {{ $kelasList->firstWhere('id', request('kelas'))->name ?? '-' }}
                        </span>
                    @endif

                    @if (request('status'))
                        <span class="badge bg-light text-dark border">Status: {{ request('status') }}</span>
                    @endif

                    @if (request('start_date'))
                        <span class="badge bg-light text-dark border">Dari: {{ request('start_date') }}</span>
                    @endif

                    @if (request('end_date'))
                        <span class="badge bg-light text-dark border">Sampai: {{ request('end_date') }}</span>
                    @endif

                    <a href="{{ url()->current() }}" class="badge bg-danger text-decoration-none">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Pengajuan</div>
                    <div class="fs-5 fw-semibold">{{ number_format($totalPengajuan) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body py-3">
                    <div class="text-muted small">Pending</div>
                    <div class="fs-5 fw-semibold">{{ number_format($totalPending) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-start border-success border-4">
                <div class="card-body py-3">
                    <div class="text-muted small">Diterima</div>
                    <div class="fs-5 fw-semibold">{{ number_format($totalDiterima) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-start border-danger border-4">
                <div class="card-body py-3">
                    <div class="text-muted small">Ditolak</div>
                    <div class="fs-5 fw-semibold">{{ number_format($totalDitolak) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body pb-1 pt-3">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">
                    Menampilkan {{ $pengajuan->firstItem() ?? 0 }}–{{ $pengajuan->lastItem() ?? 0 }}
                    dari {{ $pengajuan->total() }} pengajuan
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">ID</th>
                            <th>Nama</th>
                            <th class="text-center">Kelas</th>
                            <th class="text-center">Saldo</th>
                            <th class="text-center">Jumlah Penarikan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Alasan</th>
                            <th class="text-center">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan as $pengajuans)
                            <tr>
                                <td class="text-center">{{ $pengajuan->firstItem() + $loop->index }}</td>
                                <td class="text-center">{{ $pengajuans->user->username }}</td>
                                <td>{{ Str::limit($pengajuans->user->name, 20, '...') }}</td>
                                <td class="text-center">{{ $pengajuans->user->kelas->name ?? '-' }}</td>
                                <td class="text-center">Rp. {{ number_format($pengajuans->user->tabungan->saldo ?? 0) }}</td>
                                <td class="text-center">Rp. {{ number_format($pengajuans->jumlah_penarikan ?? 0) }}</td>
                                <td class="text-center">
                                    @if (in_array($pengajuans->status, ['Pending', 'pending']))
                                        <span class="badge bg-warning">Pending</span>
                                        <span class="badge bg-light">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @elseif (in_array($pengajuans->status, ['Batal', 'batal']))
                                        <span class="badge bg-danger">Batal</span>
                                        <span class="badge bg-light">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @elseif (in_array($pengajuans->status, ['Tolak', 'tolak']))
                                        <span class="badge bg-danger">Tolak</span>
                                        <span class="badge bg-light">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @elseif (in_array($pengajuans->status, ['Terima', 'Diterima', 'SUCCEEDED']))
                                        <span class="badge bg-success">Terima</span>
                                        <span class="badge bg-light">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                        <span class="badge bg-light">-</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($pengajuans->alasan, 20, '...') }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($pengajuans->created_at)->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 text-muted"></i>
                                    Tidak ada data yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <form method="GET" action="{{ url()->current() }}" id="perPageForm">
                        @foreach (request()->except(['perPage', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div class="d-flex align-items-center mb-3">
                            <label for="perPage" class="me-2 mb-0">Show</label>
                            <select name="perPage" id="perPage" class="form-select form-control-sm form-select-sm"
                                style="width: auto" onchange="this.form.submit()">
                                @foreach ([10, 25, 50, 75, 100] as $option)
                                    <option value="{{ $option }}" {{ request('perPage', 10) == $option ? 'selected' : '' }}>
                                        {{ $option }}
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
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterCollapse = document.getElementById('filterCollapse');
    const toggleBtn = document.getElementById('filterToggleBtn');
    const toggleText = toggleBtn.querySelector('.toggle-text');
    const toggleIcon = toggleBtn.querySelector('.toggle-icon');

    function updateBtnState(isOpen) {
        toggleText.textContent = isOpen ? 'Sembunyikan Filter' : 'Tampilkan Filter';
        toggleIcon.classList.toggle('bi-chevron-down', !isOpen);
        toggleIcon.classList.toggle('bi-chevron-up', isOpen);
    }

    updateBtnState(filterCollapse.classList.contains('show'));

    filterCollapse.addEventListener('shown.bs.collapse', () => updateBtnState(true));
    filterCollapse.addEventListener('hidden.bs.collapse', () => updateBtnState(false));
});
</script>
@endsection