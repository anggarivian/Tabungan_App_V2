@extends('layout.main')

@section('title', 'Export Data - SakuRame')

@section('content')

<style>
    .page-heading h3 {
        font-size: 1.6rem;
        background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: gradientMove 3s ease-in-out infinite;
        font-weight: 800;
    }

    @keyframes gradientMove {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .nav-tabs-custom .nav-link {
        color: #6c757d;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        background: transparent;
    }

    .nav-tabs-custom .nav-link.active {
        color: #764ba2;
        border-bottom-color: #764ba2;
        background: transparent;
    }
</style>

<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Export Data Saya</h3>
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
                    <li class="breadcrumb-item active" aria-current="page">Export Data</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-tabs-custom px-4 pt-2 border-bottom" id="exportTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="tab-tbg" data-bs-toggle="tab" data-bs-target="#pane-tbg" type="button">
                        <i class="bi bi-wallet2 me-2"></i>Tabungan
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="tab-trx" data-bs-toggle="tab" data-bs-target="#pane-trx" type="button">
                        <i class="bi bi-arrow-left-right me-2"></i>Transaksi
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="tab-pgj" data-bs-toggle="tab" data-bs-target="#pane-pgj" type="button">
                        <i class="bi bi-file-earmark-text me-2"></i>Pengajuan
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4">

                {{-- Tab: Tabungan --}}
                <div class="tab-pane fade show active" id="pane-tbg" role="tabpanel">
                    <form action="{{ route('siswa.export.tabungan') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pilih Periode</label>
                                <select class="form-select" name="periode_id" required>
                                    @foreach ($periodes as $p)
                                        <option value="{{ $p->id }}" {{ \App\Helpers\PeriodeHelper::id() == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama ?? 'Periode '.$p->id }}
                                            {{ \App\Helpers\PeriodeHelper::id() == $p->id ? '★ Aktif' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Export saldo tabungan kamu pada periode yang dipilih.</div>
                            </div>
                        </div>

                        <div class="border-top mt-4 pt-3 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-sm btn-danger px-3" name="export_type" value="pdf">
                                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                            </button>
                            <button type="submit" class="btn btn-sm btn-success px-3" name="export_type" value="excel">
                                <i class="bi bi-file-earmark-excel me-1"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tab: Transaksi --}}
                <div class="tab-pane fade" id="pane-trx" role="tabpanel">
                    <form action="{{ route('siswa.export.transaksi') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pilih Periode</label>
                                <select class="form-select" name="periode_id" required>
                                    @foreach ($periodes as $p)
                                        <option value="{{ $p->id }}" {{ \App\Helpers\PeriodeHelper::id() == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama ?? 'Periode '.$p->id }}
                                            {{ \App\Helpers\PeriodeHelper::id() == $p->id ? '★ Aktif' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text mt-1">Export seluruh riwayat transaksi kamu pada periode terpilih.</div>
                            </div>
                        </div>

                        <div class="border-top mt-4 pt-3 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-sm btn-danger px-3" name="export_type" value="pdf">
                                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                            </button>
                            <button type="submit" class="btn btn-sm btn-success px-3" name="export_type" value="excel">
                                <i class="bi bi-file-earmark-excel me-1"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tab: Pengajuan --}}
                <div class="tab-pane fade" id="pane-pgj" role="tabpanel">
                    <form action="{{ route('siswa.export.pengajuan') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pilih Periode</label>
                                <select class="form-select" name="periode_id" required>
                                    @foreach ($periodes as $p)
                                        <option value="{{ $p->id }}" {{ \App\Helpers\PeriodeHelper::id() == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama ?? 'Periode '.$p->id }}
                                            {{ \App\Helpers\PeriodeHelper::id() == $p->id ? '★ Aktif' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text mt-1">Export seluruh riwayat pengajuan penarikan kamu pada periode terpilih.</div>
                            </div>
                        </div>

                        <div class="border-top mt-4 pt-3 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-sm btn-danger px-3" name="export_type" value="pdf">
                                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                            </button>
                            <button type="submit" class="btn btn-sm btn-success px-3" name="export_type" value="excel">
                                <i class="bi bi-file-earmark-excel me-1"></i> Excel
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener("DOMContentLoaded", function () {
    let isProcessing = false;

    document.querySelectorAll('button[name="export_type"]').forEach(button => {
        button.addEventListener("click", function () {
            isProcessing = true;
            setTimeout(() => { isProcessing = false; }, 5000);

            const checkAndReload = setInterval(() => {
                if (!isProcessing) {
                    clearInterval(checkAndReload);
                    location.reload();
                }
            }, 500);
        });
    });
});
</script>
@endsection