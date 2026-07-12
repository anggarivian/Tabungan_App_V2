@extends('layout.main')

@section('title', 'Stor Tabungan Massal - SakuRame')

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
</style>
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Stor Tabungan Massal</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('walikelas.tabungan.index') }}">Tabungan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stor Massal</li>
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

    {{-- Info Rombel Card --}}
    <div class="card stat-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="mb-3 text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Rombel</h5>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <span class="d-block text-muted small">Nama Rombel</span>
                    <span class="fw-bold fs-6">{{ $rombel->name ?? '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="d-block text-muted small">Wali Kelas</span>
                    <span class="fw-bold fs-6">{{ $rombel->walikelas->name ?? '-' }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="d-block text-muted small">Total Siswa</span>
                    <span class="fw-bold fs-6">{{ count($siswa) }} Orang</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="d-block text-muted small">Stor Rombel Kemarin</span>
                    <span class="fw-bold fs-6 text-success">Rp {{ number_format($jumlahTransaksiKemarin, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert & Errors --}}
    @if(session('success'))
        <div id="alert" class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('alert-message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show pb-1 shadow-sm" role="alert">
            <strong><i class="bi bi-exclamation-triangle me-2"></i>Terjadi Kesalahan!</strong>
            <ul class="mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Card Tabel --}}
    <div class="card stat-card border-0 shadow-sm">
        <div class="card-body p-0">
            <form method="POST" action="{{ route('walikelas.tabungan.storMasalTabungan') }}" id="formStorMasal">
                @csrf
                <input type="hidden" name="rombel_id" value="{{ $rombel->id }}">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" width="60">No</th>
                                <th class="d-none d-md-table-cell">Nama</th>
                                <th class="text-end d-none d-md-table-cell" width="200">Saldo Saat Ini</th>
                                <th class="text-center" width="150">Kode Rombel</th>
                                <th class="text-end" width="250">Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswa as $index => $siswas)
                                @php
                                    $kodeUnik = $siswas->rombel->first()->pivot->kode ?? $siswas->username;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="fw-medium d-none d-md-table-cell">{{ $siswas->name }}</td>
                                    <td class="text-end text-muted d-none d-md-table-cell">
                                        Rp {{ number_format($siswas->tabungan->first()->saldo ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary px-2 py-1">{{ $kodeUnik }}</span>
                                        <input type="hidden" name="input[{{ $kodeUnik }}][kode]" value="{{ $kodeUnik }}">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm justify-content-end">
                                            <span class="input-group-text bg-transparent border-end-0 text-muted">Rp</span>
                                            <input type="text"
                                                   name="input[{{ $kodeUnik }}][stor]"
                                                   class="form-control text-end fw-bold stor-input border-start-0"
                                                   placeholder="0"
                                                   style="max-width: 180px;"
                                                   autocomplete="off"
                                                   oninput="updateCalculation()">
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-people display-6 d-block mb-2"></i>
                                        Tidak ada data siswa di rombel ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="4" class="text-end fw-bold align-middle d-none d-md-table-cell">TOTAL PENYETORAN :</td>
                                <td colspan="2" class="text-end fw-bold align-middle d-md-none">TOTAL :</td>
                                <td class="text-end p-4 align-middle">
                                    <div class="d-flex flex-column">
                                        <span class="fs-4 fw-bolder text-primary" id="total-stor">Rp 0</span>
                                        <span class="small text-muted"><span id="filled-count" class="fw-bold">0</span> dari {{ count($siswa) }} Siswa</span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="p-4 border-top d-flex justify-content-between gap-2">
                    <a href="{{ route('walikelas.tabungan.index') }}" class="btn btn-light px-4">Kembali</a>
                    <button type="submit" class="btn btn-primary px-5" id="btnSubmit" {{ $isReadOnly ? 'disabled' : '' }}>
                        <i class="bi bi-save me-2"></i>Stor Tabungan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function updateCalculation() {
        let totalStor = 0;
        let filledCount = 0;

        const inputs = document.querySelectorAll('.stor-input');

        inputs.forEach(function(input) {
            let cleanValue = input.value.replace(/\D/g, '');

            if(cleanValue !== "") {
                input.value = new Intl.NumberFormat('id-ID').format(cleanValue);
                let numValue = parseInt(cleanValue, 10);
                if (numValue > 0) {
                    filledCount++;
                    totalStor += numValue;
                }
            } else {
                input.value = "";
            }
        });

        document.getElementById('total-stor').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalStor);
        document.getElementById('filled-count').textContent = filledCount;
    }

    document.getElementById('formStorMasal').addEventListener('submit', function() {
        let btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            const firstInput = document.querySelector('.stor-input');
            if (firstInput) {
                firstInput.focus();
            }
        }, 500);
    });
</script>
@endsection