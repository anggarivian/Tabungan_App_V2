@extends('layout.main')

@section('title', 'Tabungan Harian Kelas - SakuRame')

@section('content')
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
    .page-heading h4 { font-size: 1.1rem; color: #6c757d; font-weight: 400; margin-bottom: 0; }
    @keyframes gradientMove { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }

    /* Styling Custom Card & Hover Effect */
    .stat-card { border: none; border-radius: 1rem; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }

    .icon-box { width: 54px; height: 54px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .icon-box svg { width: 28px; height: 28px; }

    /* Custom Colors for Icons */
    .bg-blue-light { background-color: #e0f2fe; color: #0284c7; }
    .bg-green-light { background-color: #dcfce7; color: #16a34a; }
    .bg-purple-light { background-color: #f3e8ff; color: #9333ea; }
    .bg-red-light { background-color: #fee2e2; color: #dc2626; }
</style>

<div class="page-heading mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <span class="text-muted small fw-bold text-uppercase tracking-wider">Rombel: {{ $rombel->name ?? 'Belum Ada Kelas' }}</span>
            <h3 class="mt-1">Informasi Tabungan</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <span class="badge text-secondary border px-3 py-2 fs-6 fw-medium shadow-sm bg-transparent">
                <i class="bi bi-calendar3 me-2"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>
</div>

<div class="page-content">
    
    <div class="row g-3 mb-3">
        {{-- Card Uang Masuk --}}
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card stat-card shadow-sm h-100 mb-0"> <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-green-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/></svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Transaksi Masuk</h6>
                        <h5 class="font-extrabold mb-1">Rp {{ number_format($transaksi_masuk ?? 0) }}</h5>
                        <small class="text-success fw-bold"><i class="bi bi-arrow-up-circle"></i> {{ $storKali ?? 0 }}x Setoran</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Uang Keluar --}}
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card stat-card shadow-sm h-100 mb-0"> <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-red-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4z"/></svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Transaksi Keluar</h6>
                        <h5 class="font-extrabold mb-1">Rp {{ number_format($transaksi_keluar ?? 0) }}</h5>
                        <small class="text-danger fw-bold"><i class="bi bi-arrow-down-circle"></i> {{ $tarikKali ?? 0 }}x Penarikan</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Saldo Tunai & Digital --}}
        <div class="col-12 col-md-12 col-xl-4">
            <div class="card stat-card shadow-sm h-100 mb-0"> <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-green-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/><path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/></svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Tunai | Digital</h6>
                        <h5 class="font-extrabold mb-0">Rp {{ number_format($jumlah_saldo_tunai ?? 0) }} | Rp {{ number_format($jumlah_saldo_digital ?? 0) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Menu Transaksi Cepat --}}
    <div class="row mb-3 position-relative" style="z-index: 999;">
        <div class="col-12">
            <div class="card stat-card mb-0 shadow-sm border-0" style="overflow: visible;">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3"><i class="bi bi-grid me-2"></i>Menu Transaksi Kelas</h5>
                    
                    <div class="d-flex flex-column flex-md-row gap-3">
                        <div class="flex-fill">
                            <a href="{{ route('walikelas.tabungan.stor') }}" class="btn btn-primary w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-person-plus-fill fs-5" style="margin-top: -18px; margin-left: -5px;"></i>
                                <span>Stor Per Orang</span>
                            </a>
                        </div>
                        <div class="flex-fill">
                            <a href="{{ route('walikelas.tabungan.stor.masal') }}" class="btn btn-primary w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-people-fill fs-5" style="margin-top: -18px; margin-left: -5px;"></i>
                                <span>Stor Kolektif / Kelas</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat Harian Kelas --}}
    <div class="card stat-card shadow-sm border-0">
        <div class="card-header pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center bg-transparent">
            <h5 class="card-title mb-0"><i class="bi bi-journal-text me-2"></i>Rekap Transaksi Harian</h5>
        </div>
        
        <div class="card-body pt-4">
            <div class="table-responsive">
                
                {{-- Sub Header Data Kelas --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-2">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-bookmark-fill text-primary me-2"></i>Data {{ $rombel->name ?? 'Belum Ada Kelas' }}
                    </h6>
                    <!-- <div class="d-flex gap-2">
                        <span class="badge bg-success px-3 py-2">
                            <i class="bi bi-arrow-down-left-circle me-1"></i> Stor: Rp {{ number_format($transaksi_masuk) }}
                        </span>
                        <span class="badge bg-danger px-3 py-2">
                            <i class="bi bi-arrow-up-right-circle me-1"></i> Tarik: Rp {{ number_format($transaksi_keluar) }}
                        </span>
                    </div> -->
                </div>

                {{-- Tabel Transaksi --}}
                <table class="table table-hover align-middle mb-4" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-end">Saldo Awal</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Saldo Akhir</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Pembuat</th>
                            <th class="text-center">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksis as $trx)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + $transaksis->firstItem() - 1 }}</td>
                                <td class="fw-medium">{{ Str::limit($trx->user->name, 25, '...') }}</td>
                                <td class="text-end text-muted">Rp {{ number_format($trx->saldo_awal ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end fw-medium {{ $trx->tipe_transaksi == 'Stor' ? 'text-success' : 'text-danger' }}">
                                    {{ $trx->tipe_transaksi == 'Stor' ? '+' : '-' }} Rp {{ number_format($trx->jumlah_transaksi ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($trx->saldo_akhir ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if ($trx->tipe_transaksi == 'Stor')
                                        <span class="badge bg-success">Stor | {{ $trx->pembayaran ?? '-' }}</span>
                                    @elseif ($trx->tipe_transaksi == 'Tarik')
                                        <span class="badge bg-danger">Tarik | {{ $trx->pembayaran ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="text-center small">{{ Str::limit($trx->pembuat, 15, '...') }}</td>
                                <td class="text-center small text-muted">
                                    {{ \Carbon\Carbon::parse($trx->created_at)->format('H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-journal-x display-4 text-muted d-block mb-3"></i>
                                    <h6 class="text-muted fw-normal">Belum ada transaksi di kelas Anda hari ini.</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

            {{-- Filter perPage & Pagination --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-2">
                <form method="GET" action="{{ request()->url() }}" class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <label for="perPage" class="me-2 small text-muted">Tampilkan</label>
                        <select name="perPage" id="perPage" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10 baris</option>
                            <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25 baris</option>
                            <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50 baris</option>
                            <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100 baris</option>
                        </select>
                    </div>
                </form>
                <div>
                    {{ $transaksis->appends(['perPage' => request('perPage')])->links('layout.pagination.bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection