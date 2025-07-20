@extends('layout.main')

@section('title') Stor Tabungan - SakuRame @endsection

@section('content')
<style>
    .success-details {
            background-color: #f1f5f9;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: left;
        }
</style>
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Stor Tabungan</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    @if(auth()->user()->roles_id == 1)
                        <li class="breadcrumb-item"><a href="{{ route ('kepsek.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 2)
                        <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 3)
                        <li class="breadcrumb-item"><a href="{{ route ('walikelas.bendahara')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 4)
                        <li class="breadcrumb-item"><a href="{{ route ('siswa.dashboard')}}">Dashboard</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Stor Tabungan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="row">
        @if($invoice != null)
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card shadow-lg mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-primary mb-3">
                            Lanjutkan Pembayaran Anda
                        </h5>
                        <p>Berikut data stor transaksi anda yang belum di selesaikan :</p>
                        <p>Saldo Anda: Rp{{ number_format($nominal, 0, ',', '.') }} ({{ $terbilang }})</p>
                        <ul class="list-group list-group-flush">
                            <div class="success-details">
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Nomor Invoice:</div>
                                    <div class="col-md-8 fw-bold">{{$invoice['external_id']}}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Nama:</div>
                                <div class="col-md-8">{{ auth()->user()->name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Kelas:</div>
                                    <div class="col-md-8">{{ auth()->user()->kelas->name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Jumlah Setoran:</div>
                                    <div class="col-md-8 fw-bold">Rp {{ number_format($invoice['amount'], 0, ',', '.') }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 text-muted">Status:</div>
                                    <div class="col-md-8">
                                        <span class="badge bg-warning">Menunggu Pembayaran</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 text-muted">Batas Waktu:</div>
                                    <div class="col-md-8 text-danger">
                                        {{ $invoice['expiry_date_carbon']->format('d M Y, H:i') }}
                                        ({{ $invoice['expiry_date_carbon']->diffForHumans() }})
                                    </div>
                                </div>
                            </div>
                        </ul>
                        <a href="{{$invoice['invoice_url']}}" class="btn btn-primary mb-2" style="width: 100%">Lanjutkan Pembayaran</a>
                        <button class="btn btn-secondary w-100" onclick="openBatalModal({{ auth()->user()->id }})">
                            Batalkan
                        </button>
                    </div>
                </div>
            </div>
        @else
            <!-- Card Form Stor Tabungan -->
            <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                <div class="card shadow-lg">
                    <div class="card-body">
                        @if(session('success'))
                            <div id="alert" class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i>
                                {{ session('alert-message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <script>
                            @if(session('alert-duration'))
                                setTimeout(function() {
                                    document.getElementById('alert').style.display = 'none';
                                }, {{ session('alert-duration') }});
                            @endif
                        </script>
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show pb-1" role="alert">
                                <strong>Terjadi Kesalahan!</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <form action="{{ route('siswa.tabungan.store') }}" method="POST">
                            @csrf
                            <h5 class="fw-bold text-primary mb-3">
                                Stor Tabungan Digital
                            </h5>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <label for="id">ID Tabungan</label>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                        <input type="text" id="id" name="id" value="{{ auth()->user()->id }}" hidden>
                                        <input type="text" class="form-control" id="tabungan_id" name="tabungan_id" value="{{ auth()->user()->username }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <label for="name">Nama</label>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                        <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <label for="kelas">Kelas</label>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                        <input type="text" class="form-control" id="kelas" name="kelas" value="{{ auth()->user()->kelas->name }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <label for="jumlah_tabungan">Tabungan Saat Ini</label>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1">Rp.</span>
                                            <input type="number" class="form-control" id="jumlah_tabungan" name="jumlah_tabungan" value="{{ auth()->user()->tabungan->saldo }}" readonly>
                                        </div>
                                        <p class="fst-italic fw-lighter text-start mb-0">*{{$terbilang}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <label for="jumlah_stor">Jumlah Stor</label>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" id="jumlah_stor" name="jumlah_stor" required min="1000" placeholder="Masukkan Jumlah Stor">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4"></div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="d-flex justify-content-between">
                                            @if(auth()->user()->username == '722')
                                                <button class="btn btn-primary" style="width: 100%" type="submit">Lanjut Bayar</button>
                                            @else
                                                <button class="btn btn-primary" style="width: 100%" disabled>Segera Hadir ... !</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Card Informasi Penting -->
            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">
                            <i class="bi bi-info-circle"></i> Informasi Penting
                        </h5>
                        <p>Penabung dapat melakukan penyetoran secara mandiri menggunakan:</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item border-0 px-0">
                                <i class="bi bi-qr-code text-success"></i> <strong>QRIS</strong><br>
                                <small class="text-muted">Pembayaran cepat dengan kode QR</small>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <i class="bi bi-bank text-warning"></i> <strong>Transfer Bank</strong><br>
                                <small class="text-muted">Kirim dana langsung ke rekening sekolah</small>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <i class="bi bi-wallet2 text-info"></i> <strong>E-Wallet</strong><br>
                                <small class="text-muted">Gunakan dompet digital pilihan Anda</small>
                            </li>
                        </ul>
                        <div class="alert alert-warning mt-3" role="alert">
                            <i class="bi bi-exclamation-triangle"></i>
                            <small>Perhatikan bahwa metode <strong>Digital</strong> memiliki biaya administrasi antara Rp. 100 ~ Rp. 5,000 tergantung besaran transaksinya.</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal Konfirmasi Batal --}}
<div class="modal fade modal-borderless" id="batalModal" tabindex="-1" aria-labelledby="batalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batalModalLabel">Konfirmasi Pembatalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin membatalkan pengajuan ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                <form id="batalForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function openBatalModal(id) {
        var form = document.getElementById('batalForm');
        form.action = '/siswa/tabungan/stor/batal/' + id;

        var myModal = new bootstrap.Modal(document.getElementById('batalModal'));
        myModal.show();
    }
</script>
@endsection
