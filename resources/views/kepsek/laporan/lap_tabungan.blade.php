@extends('layout.main')

@section('title') Laporan Tabungan - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Laporan Tabungan</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    @if(auth()->user()->roles_id == 1)
                        <li class="breadcrumb-item"><a href="{{ route ('kepsek.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 2)
                        <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 3)
                        <li class="breadcrumb-item"><a href="{{ route ('walikelas.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 4)
                        <li class="breadcrumb-item"><a href="{{ route ('siswa.dashboard')}}">Dashboard</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Laporan Tabungan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="card shadow mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-funnel"></i> Filter Laporan
                </h6>
                <button class="btn btn-sm btn-outline-primary" type="button"
                    data-bs-toggle="collapse" data-bs-target="#filterCollapse"
                    aria-expanded="false" aria-controls="filterCollapse"
                    id="filterToggleBtn">
                    <span class="toggle-text">Tampilkan Filter</span>
                    <i class="bi bi-chevron-down toggle-icon"></i>
                </button>
            </div>

            {{-- default collapsed (sembunyi), otomatis kebuka kalau ada filter aktif --}}
            <div class="collapse {{ request()->anyFilled(['search', 'kelas', 'sort_saldo', 'min_saldo', 'max_saldo']) ? 'show' : '' }} mt-3"
                id="filterCollapse">
                <form action="{{ route('laporan.kepsek.tabungan') }}" method="GET" id="filterForm">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-lg-3">
                            <label class="form-label small text-muted mb-1">Nama / Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search" style="margin-top:-10px"></i></span>
                                <input type="text" class="form-control" name="search" id="search"
                                    value="{{ request('search') }}" placeholder="Nama / Username...">
                            </div>
                        </div>

                        <div class="col-6 col-lg-2">
                            <label class="form-label small text-muted mb-1">Kelas</label>
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

                        <div class="col-6 col-lg-2">
                            <label class="form-label small text-muted mb-1">Urutkan Saldo</label>
                            <select class="form-select" name="sort_saldo" id="sort_saldo">
                                <option value="">Default</option>
                                <option value="desc" {{ request('sort_saldo') == 'desc' ? 'selected' : '' }}>Terbesar</option>
                                <option value="asc" {{ request('sort_saldo') == 'asc' ? 'selected' : '' }}>Terkecil</option>
                            </select>
                        </div>

                        <div class="col-6 col-lg-2">
                            <label class="form-label small text-muted mb-1">Saldo Min</label>
                            <input type="number" class="form-control" name="min_saldo" min="0"
                                value="{{ request('min_saldo') }}" placeholder="0">
                        </div>

                        <div class="col-6 col-lg-2">
                            <label class="form-label small text-muted mb-1">Saldo Max</label>
                            <input type="number" class="form-control" name="max_saldo" min="0"
                                value="{{ request('max_saldo') }}" placeholder="Tak terbatas">
                        </div>

                        <div class="col-12 col-lg-1 d-flex gap-1">
                            <button type="submit" class="btn btn-primary w-100" title="Cari">
                                <i class="bi bi-funnel-fill"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Badge filter aktif, taruh di luar collapse biar tetap keliatan walau filter disembunyiin --}}
            @if (request()->anyFilled(['search', 'kelas', 'sort_saldo', 'min_saldo', 'max_saldo']))
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

                    @if (request('sort_saldo'))
                        <span class="badge bg-light text-dark border">
                            Urut: {{ request('sort_saldo') == 'desc' ? 'Terbesar' : 'Terkecil' }}
                        </span>
                    @endif

                    @if (request('min_saldo'))
                        <span class="badge bg-light text-dark border">Min: Rp {{ number_format(request('min_saldo')) }}</span>
                    @endif

                    @if (request('max_saldo'))
                        <span class="badge bg-light text-dark border">Max: Rp {{ number_format(request('max_saldo')) }}</span>
                    @endif

                    <a href="{{ route('laporan.kepsek.tabungan') }}" class="badge bg-danger text-decoration-none">
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
                    <div class="text-muted small">Total Santri</div>
                    <div class="fs-5 fw-semibold">{{ number_format($totalSantri) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-start border-success border-4">
                <div class="card-body py-3">
                    <div class="text-muted small">Total Saldo (sesuai filter)</div>
                    <div class="fs-5 fw-semibold">Rp {{ number_format($totalSaldoKeseluruhan) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body pb-1 pt-3 mb-3">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">
                    Menampilkan {{ $user->firstItem() ?? 0 }}–{{ $user->lastItem() ?? 0 }}
                    dari {{ $user->total() }} data
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px">No.</th>
                            <th class="text-center">ID</th>
                            <th>Nama</th>
                            <th class="text-center">Kelas</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user as $index => $users)
                            <tr>
                                <td class="text-center">{{ $user->firstItem() + $index }}</td>
                                <td class="text-center">{{ $users->username }}</td>
                                <td>{{ $users->name }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-dark">
                                        {{ $users->kelas->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-end">Rp {{ number_format($users->tabungan->saldo ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                    Data tidak ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <form method="GET" action="{{ request()->url() }}" id="perPageForm">
                        @foreach (request()->except('perPage', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <div class="d-flex align-items-center">
                            <label for="perPage" class="me-2 mb-0">Show</label>
                            <select name="perPage" id="perPage" class="form-select form-select-sm"
                                onchange="this.form.submit()">
                                @foreach ([10, 25, 50, 75, 100] as $opt)
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
</div>
@endsection

@section('js')

@endsection
