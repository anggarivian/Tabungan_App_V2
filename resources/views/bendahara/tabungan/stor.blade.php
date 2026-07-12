@extends('layout.main')

@section('title', 'Stor Tabungan - SakuRame')

@section('content')
<style>
    .stat-card {
        border: none;
        border-radius: 1rem;
        transition: all 0.3s ease;
        background: var(--bs-card-bg, var(--bs-body-bg));
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }

    /* Panel Riwayat Transaksi */
    .riwayat-panel {
        background: var(--bs-tertiary-bg);
        border-radius: 0.75rem;
        height: 100%;
        min-height: 480px;
        display: flex;
        flex-direction: column;
    }

    .riwayat-header {
        padding: 1rem 1.25rem 0.75rem;
        border-bottom: 1px solid var(--bs-border-color);
    }

    .riwayat-scroll {
        flex: 1;
        overflow-y: auto;
        padding: 0.5rem 1rem;
        scroll-behavior: smooth;
    }

    .riwayat-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .riwayat-scroll::-webkit-scrollbar-thumb {
        background: var(--bs-border-color);
        border-radius: 10px;
    }

    .riwayat-item {
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
        border-radius: 0.6rem;
        padding: 0.55rem 0.8rem;
        margin-bottom: 0.5rem;
        border: 1px solid var(--bs-border-color);
        font-size: 0.85rem;
    }

    .riwayat-item .jumlah {
        font-weight: 700;
        font-size: 0.95rem;
    }

    .riwayat-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--bs-secondary-color);
        text-align: center;
        padding: 2rem 1rem;
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

{{-- ================= SNIPPET CEK RENTANG PERIODE (UI) ================= --}}
@php
    $activePeriode = \App\Models\Periode::find(\App\Helpers\PeriodeHelper::id());
    $isReadOnly = true; 
    
    if($activePeriode) {
        $now = \Carbon\Carbon::now();
        $mulai = \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->startOfDay();
        $akhir = \Carbon\Carbon::parse($activePeriode->tanggal_berakhir)->endOfDay();
        
        if($now->between($mulai, $akhir)) {
            $isReadOnly = false;
        }
    }
@endphp

@if($isReadOnly)
    <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm" role="alert">
        <i class="bi bi-shield-lock-fill fs-4 me-3" style="margin-top: -25px;"></i>
        <div>
            <strong>Mode Hanya Baca!</strong><br>
            <small>Anda tidak bisa melakukan perubahan data atau transaksi dikarenakan hari ini berada di luar rentang tanggal ({{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($activePeriode->tanggal_berakhir)->format('d M Y') }}) periode ini.</small>
        </div>
    </div>
@endif

    <div class="card stat-card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-12 col-lg-7">

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
                            <button type="submit" class="btn btn-primary px-5" {{ $isReadOnly ? 'disabled' : '' }}><i class="bi bi-save me-2"></i>Simpan Stor</button>
                        </div>
                    </form>
                </div>

                {{-- Panel Riwayat Transaksi --}}
                <div class="col-12 col-lg-5">
                    <div class="riwayat-panel">
                        <div class="riwayat-header">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0"><i class="bi bi-clock-history me-1"></i> Riwayat Transaksi</h6>
                                <span id="riwayat-nama" class="badge bg-body-secondary text-body border small"></span>
                            </div>
                        </div>

                        <div class="riwayat-scroll" id="riwayat-scroll">
                            <div class="riwayat-empty" id="riwayat-empty">
                                <i class="bi bi-search fs-2 mb-2"></i>
                                <br>
                                <p class="mb-0 small">Cari kode siswa untuk melihat<br>riwayat transaksinya di sini</p>
                            </div>
                            <div id="riwayat-list"></div>
                        </div>
                    </div>
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

        function renderRiwayat(riwayat, nama) {
            var $list = $('#riwayat-list');
            var $empty = $('#riwayat-empty');
            var $scroll = $('#riwayat-scroll');

            $('#riwayat-nama').text(nama || '');

            if (!riwayat || riwayat.length === 0) {
                $list.empty();
                $empty.html('<i class="bi bi-inbox fs-2 mb-2"></i><br><p class="mb-0 small">Belum ada riwayat transaksi<br>untuk siswa ini di periode berjalan</p>').show();
                return;
            }

            $empty.hide();

            var html = '';
            riwayat.forEach(function(item) {
                var isStor = item.tipe === 'stor';
                var badgeClass = isStor ? 'bg-success' : 'bg-danger';
                var badgeLabel = isStor ? 'Stor' : 'Tarik';
                var sign = isStor ? '+' : '-';
                var amountClass = isStor ? 'text-success' : 'text-danger';

                html += '<div class="riwayat-item">'
                    + '<div class="d-flex justify-content-between align-items-start">'
                    + '<span class="badge ' + badgeClass + '">' + badgeLabel + '</span>'
                    + '<span class="text-muted" style="font-size:0.75rem">' + item.tanggal + '</span>'
                    + '</div>'
                    + '<div class="d-flex justify-content-between align-items-center mt-1">'
                    + '<span class="jumlah ' + amountClass + '">' + sign + ' Rp ' + item.jumlah + '</span>'
                    + '<span class="text-muted" style="font-size:0.75rem">Saldo: Rp ' + item.saldo_akhir + '</span>'
                    + '</div>'
                    + '</div>';
            });

            $list.html(html);

            // auto-scroll ke bawah biar transaksi terbaru langsung kelihatan
            $scroll.animate({ scrollTop: $scroll[0].scrollHeight }, 300);
        }

        // Handle Form Search
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            var kode = $('#kode').val();

            $.ajax({
                url: "{{ route('bendahara.search') }}",
                method: "GET",
                data: {
                    kode: kode,
                    timestamp: new Date().getTime()
                },
                success: function(data) {
                    if (data) {
                        if (data.name !== 'Tidak Ada') {
                            $('#name').val(data.name);
                            $('#rombel').val(data.rombel);
                            $('#jumlah_tabungan').val(data.tabungan);
                            $('#kode2').val(kode);

                            $('#tidak-ada').empty();
                            $('#jumlah_stor').focus();

                            renderRiwayat(data.riwayat, data.name);
                        } else {
                            $('#tidak-ada').html('<div class="alert alert-danger py-2 small mb-0"><i class="bi bi-x-circle me-1"></i> Data tidak ditemukan di rombel aktif.</div>');
                            setTimeout(function() {
                                $('#tidak-ada').empty();
                            }, 3000);

                            $('#name').val('');
                            $('#rombel').val('');
                            $('#jumlah_tabungan').val('');
                            $('#kode2').val('');

                            renderRiwayat([], '');
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

        $('#kode').on('change', function() {
            $('#kode2').val($(this).val());
        });
    });
</script>
@endsection