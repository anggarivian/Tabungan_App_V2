@extends('layout.main')

@section('title') Stor Tabungan - SakuRame @endsection

@section('content')
<div class="page-heading mb-2">
    <div class="d-flex justify-content-between">
        <h3 class="mt-3">Stor Tabungan</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-right">
                @if(auth()->user()->roles_id == 1)
                    <li class="breadcrumb-item"><a href="{{ route ('kepsek.dashboard')}}">Dashboard</a></li>
                @elseif(auth()->user()->roles_id == 2)
                    <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                @elseif(auth()->user()->roles_id == 3)
                    <li class="breadcrumb-item"><a href="{{ route ('walikelas.bendahara')}}">Bendahara</a></li>
                @elseif(auth()->user()->roles_id == 4)
                    <li class="breadcrumb-item"><a href="{{ route ('siswa.dashboard')}}">Siswa</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">Stor Tabungan</li>
            </ol>
        </nav>
    </div>
</div>
<div class="page-content">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-primary mb-3">
                        Informasi Penting
                    </h5>
                    <p>Penabung dapat melakukan penyetoran secara mandiri menggunakan:</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0">
                            <i class="bi bi-qr-code text-success"></i> <strong>QRIS</strong> – Pembayaran cepat dengan kode QR.
                        </li>
                        <li class="list-group-item border-0">
                            <i class="bi bi-bank text-warning"></i> <strong>Transfer Bank</strong> – Kirim dana langsung ke rekening sekolah.
                        </li>
                        <li class="list-group-item border-0">
                            <i class="bi bi-wallet2 text-info"></i> <strong>E-Wallet</strong> – Gunakan dompet digital pilihan Anda.
                        </li>
                    </ul>
                    <p class="mt-3 text-muted">
                        * Perhatikan bahwa setiap metode memiliki biaya administrasi yang berbeda.
                    </p>
                </div>
            </div>

        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card">
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
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn btn-primary" style="width: 100%">Lanjutkan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection
