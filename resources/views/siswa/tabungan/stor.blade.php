@extends('layout.main')

@section('title', 'Stor Tabungan - SakuRame')

@section('content')
<style>
    .success-details {
        background-color: #f1f5f9;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        text-align: left;
    }
    /* Styling Custom Card & Hover Effect */
    .stat-card { border: none; border-radius: 1rem; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
</style>

<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Stor Tabungan Digital</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stor Tabungan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="row">
        @if($invoice != null)
            <div class="col-12">
                <div class="card stat-card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-primary mb-3">Lanjutkan Pembayaran Anda</h5>
                        <p>Berikut data stor transaksi Anda yang belum diselesaikan:</p>
                        <p class="mb-4">Saldo Anda Saat Ini: <strong>Rp {{ number_format($nominal, 0, ',', '.') }}</strong> <br><small class="text-muted">({{ $terbilang }})</small></p>
                        
                        <div class="success-details">
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Nomor Invoice:</div>
                                <div class="col-md-8 fw-bold">{{ $invoice['external_id'] }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Nama:</div>
                                <div class="col-md-8">{{ auth()->user()->name }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Kelas / Rombel:</div>
                                <div class="col-md-8">{{ $rombel->name ?? 'Belum ada kelas' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Jumlah Setoran:</div>
                                <div class="col-md-8 fw-bold text-success">Rp {{ number_format($invoice['amount'], 0, ',', '.') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 text-muted">Status:</div>
                                <div class="col-md-8">
                                    <span class="badge bg-warning ">Menunggu Pembayaran</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-muted">Batas Waktu:</div>
                                <div class="col-md-8 text-danger fw-medium">
                                    {{ $invoice['expiry_date_carbon']->format('d M Y, H:i') }}
                                    ({{ $invoice['expiry_date_carbon']->diffForHumans() }})
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ $invoice['invoice_url'] }}" class="btn btn-primary flex-fill">Lanjutkan Pembayaran</a>
                            <button class="btn btn-secondary flex-fill" onclick="openBatalModal({{ auth()->user()->id }})">Batalkan Transaksi</button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12 col-md-8">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        @if(session('success'))
                            <div id="alert" class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-1"></i> {{ session('alert-message') ?? session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                <strong>Gagal!</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('siswa.tabungan.store') }}" method="POST">
                            @csrf
                            <h5 class="fw-bold text-primary mb-4">Form Setor Digital</h5>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">ID / Username</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control " value="{{ auth()->user()->username }}" readonly>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Nama Lengkap</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control " value="{{ auth()->user()->name }}" readonly>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Rombel Aktif</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control " value="{{ $rombel->name ?? 'Belum ada rombel' }}" readonly>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Tabungan Saat Ini</label>
                                <div class="col-sm-8">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text ">Rp</span>
                                        <input type="text" class="form-control  fw-bold" value="{{ number_format($nominal, 0, ',', '.') }}" readonly>
                                    </div>
                                    <small class="fst-italic text-muted">*{{ $terbilang }}</small>
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="jumlah_stor" class="col-sm-4 col-form-label fw-bold">Jumlah Setoran</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control form-control-lg" id="jumlah_stor" name="jumlah_stor" required min="10000" placeholder="Minimal 10.000">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <button class="btn btn-primary w-100 py-2 fw-bold" type="submit">
                                        <i class="bi bi-shield-check me-2"></i> Lanjutkan Pembayaran
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">
                            <i class="bi bi-info-circle me-1"></i> Metode Pembayaran
                        </h5>
                        <p class="small text-muted">Anda dapat melakukan penyetoran secara mandiri 24/7 menggunakan jaringan Xendit:</p>
                        
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item px-0">
                                <i class="bi bi-qr-code text-success fs-5 me-2 align-middle"></i> 
                                <span class="fw-medium">QRIS</span>
                            </li>
                            <li class="list-group-item px-0">
                                <i class="bi bi-bank text-primary fs-5 me-2 align-middle"></i> 
                                <span class="fw-medium">Virtual Account Bank</span>
                            </li>
                            <li class="list-group-item px-0">
                                <i class="bi bi-wallet2 text-info fs-5 me-2 align-middle"></i> 
                                <span class="fw-medium">E-Wallet</span> (Dana, Ovo, dll)
                            </li>
                            <li class="list-group-item px-0">
                                <i class="bi bi-shop text-danger fs-5 me-2 align-middle"></i> 
                                <span class="fw-medium">Gerai Ritel</span> (Alfamart / Indomaret)
                            </li>
                        </ul>

                        <div class="alert alert-warning py-2 small mb-0" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Perhatikan bahwa metode <strong>Digital</strong> dapat memiliki biaya administrasi antara Rp 1.000 ~ Rp 5.000 yang dibebankan oleh payment gateway.
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal Konfirmasi Batal --}}
<div class="modal fade" id="batalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger pb-3">
                <h5 class="modal-title text-white"><i class="bi bi-exclamation-octagon me-2"></i>Batalkan Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="mb-0">Apakah Anda yakin ingin membatalkan pengajuan setoran digital ini? <br> Link pembayaran/QRIS akan menjadi tidak valid.</p>
            </div>
            <div class="modal-footer ">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                <form id="batalForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">Ya, Batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Auto-hide alert
    @if(session('alert-duration') || session('success'))
        setTimeout(function() {
            let alertEl = document.getElementById('alert');
            if (alertEl) {
                let bsAlert = new bootstrap.Alert(alertEl);
                bsAlert.close();
            }
        }, 5000); // Set ke 5 detik standar
    @endif

    function openBatalModal(id) {
        var form = document.getElementById('batalForm');
        form.action = '/siswa/tabungan/stor/batal/' + id;
        var myModal = new bootstrap.Modal(document.getElementById('batalModal'));
        myModal.show();
    }
</script>
@endsection