@extends('layout.main')

@section('title', 'Stor Tabungan - SakuRame')

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
            <h3 class="mt-3">Stor Tabungan (Per Orang)</h3>
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
                    <li class="breadcrumb-item active" aria-current="page">Stor</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card stat-card shadow-sm border-0">
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
                    <form id="searchForm" class="mb-4 p-3 rounded border">
                        <div class="form-group mb-0">
                            <label for="kode" class="form-label fw-bold">Cari Kode Siswa <small class="text-muted fw-normal">(Contoh: 1A01)</small></label>
                            <div class="input-group">
                                <input type="text" id="kode" class="form-control" placeholder="Ketik lalu Enter..." autocomplete="off" autofocus>
                                <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search me-2"></i>Cari</button>
                            </div>
                            <div id="tidak-ada" class="mt-2"></div>
                        </div>
                    </form>

                    {{-- Form Input Transaksi --}}
                    <form action="{{ route('bendahara.tabungan.storTabungan') }}" method="POST">
                        @csrf
                        {{-- Hidden Input untuk lempar kode ke Controller --}}
                        <input type="hidden" id="kode2" name="kode">
                        
                        <div class="row mb-3">
                            <label for="name" class="col-sm-4 col-form-label text-muted">Nama Siswa</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="name" name="name" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="rombel" class="col-sm-4 col-form-label text-muted">Rombel</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="rombel" name="rombel" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="jumlah_tabungan" class="col-sm-4 col-form-label text-muted">Saldo Saat Ini</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-text border-end-0">Rp</span>
                                    <input type="text" class="form-control border-start-0" id="jumlah_tabungan" readonly>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row mb-4">
                            <label for="jumlah_stor" class="col-sm-4 col-form-label fw-bold">Nominal Stor</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-success fw-bold">Rp</span>
                                    <input type="text" class="form-control form-control-lg text-success fw-bold" id="jumlah_stor" name="jumlah_stor" placeholder="0" autocomplete="off" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('bendahara.tabungan.index') }}" class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-5"><i class="bi bi-save me-2"></i>Simpan Stor</button>
                        </div>
                    </form>
                </div>
                
                {{-- Ilustrasi Samping --}}
                <div class="col-12 col-md-5 col-lg-5 d-none d-md-flex align-items-center justify-content-center border-start">
                    <img src="{{ asset('dist/assets/compiled/jpg/2.jpg') }}" class="img-fluid rounded-4 shadow-sm" style="max-height: 350px; object-fit: cover;" alt="Ilustrasi Tabungan">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        
        // Handle Form Search
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            var kode = $('#kode').val();

            $.ajax({
                // Pastikan route ini sesuai dengan nama route di web.php lu (contoh: route('bendahara.tabungan.search'))
                url: "{{ route('bendahara.search') }}", 
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
                            $('#kode2').val(kode); // Isi hidden input untuk disubmit

                            $('#tidak-ada').empty();
                            $('#jumlah_stor').focus();
                        } else {
                            $('#tidak-ada').html('<div class="alert alert-danger py-2 small mb-0"><i class="bi bi-x-circle me-1"></i> Data tidak ditemukan di rombel aktif.</div>');
                            setTimeout(function() {
                                $('#tidak-ada').empty();
                            }, 3000);
                            
                            // Reset form bawah
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

        // Format angka (Ribuan) otomatis saat ngetik
        $('#jumlah_stor').on('input', function () {
            var value = $(this).val().replace(/\D/g, '');
            var formatted = new Intl.NumberFormat('id-ID').format(value);
            $(this).val(formatted);
        });

        // Sync hidden input kalau user ngetik tapi nggak nge-submit (opsional)
        $('#kode').on('change', function() {
            $('#kode2').val($(this).val());
        });
    });
    
</script>
@endsection