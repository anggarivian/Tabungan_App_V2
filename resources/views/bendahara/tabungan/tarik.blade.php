@extends('layout.main')

@section('title', 'Tarik Tabungan - SakuRame')

@section('content')
<style>
    /* Styling Custom Card & Hover Effect */
    .stat-card {
        border: none;
        border-radius: 1rem;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
</style>
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Tarik Tabungan Tunai</h3>
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
                    @endif
                    <li class="breadcrumb-item"><a href="{{ route('bendahara.tabungan.index') }}">Tabungan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tarik</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-12 col-md-7 col-lg-7 pe-md-4">
                    
                    {{-- Alert Messages --}}
                    @if(session('success'))
                        <div id="alert" class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('alert-message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <script>
                            @if(session('alert-duration'))
                                setTimeout(function() {
                                    document.getElementById('alert').style.display = 'none';
                                }, {{ session('alert-duration') }});
                            @endif
                        </script>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show pb-1" role="alert">
                            <strong><i class="bi bi-exclamation-triangle me-2"></i>Terjadi Kesalahan!</strong>
                            <ul class="mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Form Pencarian --}}
                    <form id="searchForm" class="mb-4">
                        <div class="form-group mb-0 p-3 rounded border">
                            <label for="kode" class="form-label fw-bold">Cari Kode Siswa <small class="text-muted fw-normal">(Contoh: 1A01)</small></label>
                            <div class="input-group">
                                <input type="text" id="kode" class="form-control" placeholder="Ketik lalu Enter..." autocomplete="off" autofocus>
                                <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search me-2"></i>Cari</button>
                            </div>
                            <div id="tidak-ada" class="mt-2"></div>
                        </div>
                    </form>

                    {{-- Form Input Tarik --}}
                    <form action="{{ route('bendahara.tabungan.tarikTabungan') }}" method="POST">
                        @csrf
                        <input type="hidden" id="kode2" name="kode">
                        
                        {{-- Pakai form-control biasa + readonly biar aman di semua mode --}}
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-4 col-form-label text-muted">Nama Siswa</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="name" name="name" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-4 col-form-label text-muted">Rombel</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="rombel" name="rombel" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-4 col-form-label fw-bold">Saldo Saat Ini</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control fw-bold" id="jumlah_tabungan" readonly>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row mb-3 align-items-center">
                            <label for="jumlah_tarik" class="col-sm-4 col-form-label fw-bold">Jumlah Penarikan</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-danger text-white border-danger">Rp</span>
                                    <input type="text" class="form-control form-control-lg text-danger fw-bold border-danger" id="jumlah_tarik" name="jumlah_tarik" placeholder="0" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4 align-items-center">
                            <label for="premi" class="col-sm-4 col-form-label text-muted">Biaya Admin (5%)</label>
                            <div class="col-sm-8">
                                <div class="input-group input-group-sm mb-1">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control text-muted fw-bold" id="premi" name="premi" value="0" readonly>
                                </div>
                                <small class="text-muted d-block lh-1" style="font-size: 0.75rem;">*Dibulatkan ke kelipatan seribu ke atas.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('bendahara.tabungan.index') }}" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-danger px-5"><i class="bi bi-box-arrow-right me-2"></i>Tarik Dana</button>
                        </div>
                    </form>
                </div>
                
                {{-- Ilustrasi --}}
                <div class="col-12 col-md-5 col-lg-5 d-none d-md-flex align-items-center justify-content-center border-start">
                    {{-- Tambahin object-fit: cover dan aspect-ratio biar ga gepeng --}}
                    <img src="{{ asset('dist/assets/compiled/jpg/2.jpg') }}" class="img-fluid rounded-4 shadow-sm" style="width: 100%; max-width: 300px; aspect-ratio: 1/1; object-fit: cover;" alt="Ilustrasi Tabungan">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Logic Kalkulasi Admin 5%
    function bulatkanKeRibuan(angka) {
        return Math.round(angka / 1000) * 1000;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const jumlahTarikInput = document.getElementById('jumlah_tarik');
        const premiInput = document.getElementById('premi');

        jumlahTarikInput.addEventListener('input', function () {
            let value = jumlahTarikInput.value.replace(/\D/g, '');
            let jumlahTarik = parseInt(value) || 0;

            let biayaAdmin = jumlahTarik * 0.05;
            let dibulatkan = bulatkanKeRibuan(biayaAdmin);

            premiInput.value = new Intl.NumberFormat('id-ID').format(dibulatkan);
        });
    });

    // Logic Pencarian AJAX & Formatting
    $(document).ready(function(){
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            var kode = $('#kode').val();

            $.ajax({
                url: "{{ route('bendahara.search') }}", // Memakai route search yang sama dengan fitur stor
                method: "GET",
                data: {
                    kode: kode,
                    timestamp: new Date().getTime()
                },
                success: function(data) {
                    if (data) {
                        if(data.name !== 'Tidak Ada') {
                            $('#name').val(data.name);
                            $('#rombel').val(data.rombel);
                            $('#jumlah_tabungan').val(data.tabungan);
                            $('#kode2').val(kode); 

                            $('#tidak-ada').empty();
                            $('#jumlah_tarik').focus();
                        } else {
                            $('#tidak-ada').html('<div class="alert alert-danger py-2 small mb-0"><i class="bi bi-x-circle me-1"></i> Data tidak ditemukan di rombel aktif.</div>');
                            setTimeout(function() {
                                $('#tidak-ada').empty();
                            }, 3000);
                            
                            $('#name').val('');
                            $('#rombel').val('');
                            $('#jumlah_tabungan').val('');
                            $('#kode2').val('');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan jaringan: ' + error);
                }
            });
        });

        $('#jumlah_tarik').on('input', function () {
            var value = $(this).val().replace(/\D/g, ''); 
            var formatted = new Intl.NumberFormat('id-ID').format(value);
            $(this).val(formatted);
        });

        $('#kode').on('change', function() {
            $('#kode2').val($(this).val());
        });
        
        setTimeout(function(){
            $('#kode').focus();
        }, 500);
    });
</script>
@endsection