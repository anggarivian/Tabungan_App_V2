@extends('layout.main')

@section('title', 'Kelola Pengajuan Penarikan - SakuRame')

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Pengajuan Penarikan</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pengajuan</li>
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

    <div class="card shadow">
        <div class="card-body" style="margin-bottom: -20px">
            @if(session('success'))
                <div id="alert" class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('alert-message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h5 class="card-title mb-1">Data Pengajuan Penarikan Tabungan</h5>
                    <small class="text-muted d-block mb-3">Periode Aktif: Tarik data otomatis sesuai kalender akademik</small>
                    
                    {{-- Tampilan Saldo Xendit --}}
                    @if($saldoXendit !== null)
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary fs-6 px-3 py-2">
                            <i class="bi bi-wallet2 me-2"></i>Saldo Kas Digital (Xendit): <strong>Rp {{ number_format($saldoXendit, 0, ',', '.') }}</strong>
                        </span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger fs-6 px-3 py-2">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal memuat saldo Xendit
                        </span>
                    @endif
                </div>
                
                <form action="/bendahara/kelola-pengajuan" method="GET" class="w-100 w-md-auto mt-2 mt-md-0">
                    <div class="input-group">
                        <input type="text" class="form-control rounded" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nama atau rombel...">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body pb-1 pt-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px">No.</th>
                            <th class="text-center" style="width: 100px">ID</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">Rombel</th>
                            <th>Alasan</th>
                            <th class="text-end">Jumlah Penarikan</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan as $index => $pengajuans)
                            <tr>
                                <td class="text-center">{{ $penganuan->firstItem ?? $loop->iteration }}</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $pengajuans->user->username }}</span></td>
                                <td class="fw-medium">{{ Str::limit($pengajuans->user->name, 25, '...') }}</td>
                                <td class="text-center">
                                    <span class="badge border text-muted">
                                        {{ $pengajuans->user->rombel->first()->name ?? '-' }}
                                    </span>
                                </td>
                                <td><small class="text-muted" title="{{ $pengajuans->alasan }}">{{ Str::limit($pengajuans->alasan, 25, '...') }}</small></td>
                                <td class="text-end fw-bold">Rp {{ number_format($pengajuans->jumlah_penarikan ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center small">{{ \Carbon\Carbon::parse($pengajuans->created_at)->format('d M Y H:i') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">Pending</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-success px-3 btn-proses" data-id="{{ $pengajuans->id }}" {{ $isReadOnly ? 'disabled' : '' }}>
                                        Proses
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-6 d-block mb-2 text-secondary"></i>
                                    Tidak ada antrean pengajuan penarikan tabungan saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <form method="GET" action="{{ request()->url() }}">
                        <div class="d-flex align-items-center gap-2">
                            <label for="perPage" class="small text-muted mb-0">Show</label>
                            <select name="perPage" id="perPage" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </form>
                    <div>
                        {{ $pengajuan->appends(['perPage' => request('perPage')])->links('layout.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="editModalLabel"><i class="bi bi-gear-fill text-primary me-2"></i>Proses Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editForm" method="POST" action="#">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2"><i class="bi bi-person-fill me-1"></i> Data Siswa</h6>
                            <input type="hidden" id="edit-pengajuan-id">

                            <div class="row align-items-center mb-3">
                                <div class="col-4 small text-muted">ID Tabungan</div>
                                <div class="col-8"><input type="text" id="edit-username" class="form-control-plaintext fw-medium py-0" readonly></div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4 small text-muted">Nama</div>
                                <div class="col-8"><input type="text" id="edit-name" class="form-control-plaintext fw-medium py-0" readonly></div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4 small text-muted">Rombel</div>
                                <div class="col-8"><input type="text" id="edit-kelas" class="form-control-plaintext fw-medium py-0" readonly></div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4 small text-muted">Saldo Tabungan</div>
                                <div class="col-8">
                                    <input type="text" id="edit-tabungan" class="form-control bg-light border-0 fw-bold text-dark rounded px-2 py-1" readonly>
                                </div>
                            </div>

                            <div class="row align-items-start mb-3">
                                <div class="col-4 small text-muted">Alasan</div>
                                <div class="col-8">
                                    <textarea id="edit-alasan" class="form-control bg-light border-0 small text-dark" rows="2" readonly></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2"><i class="bi bi-credit-card-2-back-fill me-1"></i> Detail Pembayaran</h6>

                            <div class="row align-items-center mb-3">
                                <div class="col-4 small text-muted">Jenis Penarikan</div>
                                <div class="col-8"><input type="text" id="edit-pembayaran" class="form-control-plaintext fw-bold py-0" readonly></div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4 small text-muted">Jumlah Tarik</div>
                                <div class="col-8">
                                    <input type="text" id="edit-jumlah_penarikan" class="form-control bg-success bg-opacity-10 border-0 rounded px-2 py-1 text-success fw-bold" readonly>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4 small text-muted">Metode</div>
                                <div class="col-8"><input type="text" id="edit-metode_digital" class="form-control-plaintext py-0" readonly></div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4 small text-muted">Tujuan/Layanan</div>
                                <div class="col-8"><input type="text" id="edit-type_tujuan" class="form-control-plaintext py-0" readonly></div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4 small text-muted">No. Rek / HP</div>
                                <div class="col-8"><input type="text" id="edit-nomor_tujuan" class="form-control-plaintext fw-bold py-0" readonly></div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4 small text-muted">Biaya Admin (5%)</div>
                                <div class="col-8">
                                    <input type="text" id="edit-premi" class="form-control bg-warning bg-opacity-10 border-0 rounded px-2 py-1 text-warning fw-bold" readonly>
                                    <small class="text-muted d-block mt-1">*Pembulatan ke ribuan terdekat</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" id="tolakButton" class="btn btn-outline-danger px-4 fw-bold">
                        <i class="bi bi-x-circle me-1"></i> Tolak Pengajuan
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Setujui & Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let currentPengajuanId = null;

    // Ketika tombol "Proses" diklik
    $('.btn-proses').on('click', function() {
        currentPengajuanId = $(this).data('id');

        $.ajax({
            url: "/bendahara/pengajuan/get-data/" + currentPengajuanId,
            type: 'GET',
            success: function(response) {
                if (response) {
                    $('#edit-pengajuan-id').val(response.pengajuan_id);
                    $('#edit-username').val(response.username);
                    $('#edit-name').val(response.name);
                    $('#edit-kelas').val(response.kelas);
                    $('#edit-pembayaran').val(response.pembayaran);
                    $('#edit-alasan').val(response.alasan);
                    $('#edit-metode_digital').val(response.metode_digital);
                    
                    // Format mata uang untuk modal
                    let formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
                    
                    $('#edit-tabungan').val(formatter.format(response.tabungan));
                    $('#edit-jumlah_penarikan').val(formatter.format(response.jumlah_penarikan));
                    
                    if(response.pembayaran === 'Digital') {
                        $('#edit-type_tujuan').val(response.type_tujuan);
                        $('#edit-nomor_tujuan').val(response.nomor_tujuan);
                    } else {
                        $('#edit-type_tujuan').val('-');
                        $('#edit-nomor_tujuan').val('-');
                    }

                    // Hitung biaya admin 5% dan pembulatan ke ribuan terdekat
                    let jumlah = parseInt(response.jumlah_penarikan) || 0;
                    let admin = Math.ceil(jumlah * 0.05);
                    let dibulatkan = Math.ceil(admin / 1000) * 1000;
                    $('#edit-premi').val(formatter.format(dibulatkan));

                    let jumlah = parseInt(response.jumlah_penarikan) || 0;
                    let admin = Math.ceil(jumlah * 0.05);
                    let dibulatkan = Math.ceil(admin / 1000) * 1000;
                    let totalTarikSiswa = jumlah + dibulatkan;

                    if (response.pembayaran === 'Digital' && response.saldo_xendit < totalTarikSiswa) {
                        $('#btn-submit-terima').attr('disabled', true)
                                            .removeClass('btn-primary')
                                            .addClass('btn-danger')
                                            .text('Saldo Xendit Sekolah Kurang');
                    } else {
                        $('#btn-submit-terima').attr('disabled', false)
                                            .removeClass('btn-danger')
                                            .addClass('btn-primary')
                                            .html('<i class="bi bi-check-circle me-1"></i> Setujui & Transfer');
                    }
                    
                    // Update action form agar dinamis mengarah ke persetujuan penarikan
                    $('#editForm').attr('action', '/bendahara/pengajuan/setuju/' + response.pengajuan_id);
                } else {
                    alert('Gagal mengambil data');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengambil data');
            }
        });

        $('#editModal').modal('show');
    });

    // Aksi Tombol Tolak
    $('#tolakButton').on('click', function() {
        var pengajuanId = $('#edit-pengajuan-id').val();
        if (pengajuanId) {
            if(confirm('Apakah Anda yakin ingin menolak pengajuan penarikan ini?')) {
                window.location.href = '/bendahara/pengajuan/tolak/' + pengajuanId;
            }
        } else {
            alert('ID pengajuan tidak ditemukan.');
        }
    });

    // Auto-hide alert banner
    setTimeout(function() {
        let alertEl = document.getElementById('alert');
        if (alertEl) {
            let bsAlert = new bootstrap.Alert(alertEl);
            bsAlert.close();
        }
    }, 5000);
});
</script>
@endsection