@extends('layout.main')

@section('title', 'Ajukan Tarik Tabungan - SakuRame')

@section('content')
<style>
    .success-details {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        text-align: left;
    }
    /* Styling Custom Card & Hover Effect */
    .stat-card { border: none; border-radius: 1rem; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
</style>

@php    
    // Persiapan data agar sesuai dengan struktur database baru (Multi-periode)
    $periodeAktif = \App\Helpers\PeriodeHelper::id();
    $userId = auth()->user()->id;
    
    // Ambil rombel aktif
    $rombel = auth()->user()->rombel()->where('rombels.periode_id', $periodeAktif)->first();
    
    // Ambil saldo aktif
    $tabungan = \App\Models\Tabungan::where('user_id', $userId)->where('periode_id', $periodeAktif)->first();
    $saldoSaatIni = $tabungan ? $tabungan->saldo : 0;
    $terbilang = \App\Helpers\RupiahHelper::terbilangRupiah($saldoSaatIni);
    
    // Cek pengajuan yang masih pending di periode ini
    $pengajuan = \App\Models\Pengajuan::where('user_id', $userId)
                    ->where('periode_id', $periodeAktif)
                    ->latest()
                    ->first();
@endphp

<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-2">Ajukan Tarik Tabungan</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pengajuan Tarik</li>
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

    <div class="row">
        <div class="col-12 col-md-8">
            
            {{-- WADAH CARD STATUS (Akan disembunyikan oleh JS jika tombol tutup diklik) --}}
            <div id="status-card-container">
                @if($pengajuan && $pengajuan->status == 'Pending')
                    <div class="card shadow border-warning mb-4">
                        <div class="card-body p-5 text-center">
                            <i class="bi bi-hourglass-split text-warning display-1 mb-3 d-block"></i>
                            <h4 class="fw-bold">Menunggu Persetujuan Bendahara</h4>
                            <p class="text-muted mb-4">Pengajuan penarikan tabungan sebesar <strong>Rp {{ number_format($pengajuan->jumlah_penarikan, 0, ',', '.') }}</strong> sedang dalam tahap peninjauan. Mohon ditunggu.</p>
                            <button class="btn btn-outline-danger fw-bold px-4" onclick="openBatalModal({{ $pengajuan->id }})" {{ $isReadOnly ? 'disabled' : '' }}>Batalkan Pengajuan</button>
                        </div>
                    </div>

                @elseif($pengajuan && $pengajuan->status == 'PROCESSING')
                    <div class="card shadow border-info mb-4">
                        <div class="card-body p-5 text-center">
                            <div class="spinner-grow text-info mb-3" style="width: 4rem; height: 4rem;" role="status"></div>
                            <h4 class="fw-bold text-info">Transfer Sedang Diproses</h4>
                            <p class="text-muted mb-0">Bendahara telah menyetujui pengajuan Anda. Sistem sedang mentransfer dana secara otomatis ke rekening/e-wallet Anda melalui Xendit.</p>
                            <button onclick="window.location.reload()" class="btn btn-info text-white mt-4 px-4 fw-bold">Muat Ulang Status</button>
                        </div>
                    </div>

                @elseif($pengajuan && in_array($pengajuan->status, ['SUCCEEDED', 'Diterima']))
                    <div class="card shadow border-success mb-4">
                        <div class="card-body p-5 text-center bg-success bg-opacity-10 rounded">
                            <i class="bi bi-check-circle-fill text-success display-1 mb-3 d-block"></i>
                            <h4 class="fw-bold text-success">Penarikan Berhasil!</h4>
                            <p class="text-dark mb-4">Dana tabungan Anda sebesar <strong>Rp {{ number_format($pengajuan->jumlah_penarikan, 0, ',', '.') }}</strong> telah berhasil dicairkan.</p>
                            <button onclick="tampilkanForm()" class="btn btn-success px-4 fw-bold" {{ $isReadOnly ? 'disabled' : '' }}>Tutup & Ajukan Baru</button>
                        </div>
                    </div>

                @elseif($pengajuan && $pengajuan->status == 'Tolak')
                    <div class="card shadow border-danger mb-4">
                        <div class="card-body p-5 text-center bg-danger bg-opacity-10 rounded">
                            <i class="bi bi-x-octagon-fill text-danger display-1 mb-3 d-block"></i>
                            <h4 class="fw-bold text-danger">Pengajuan Ditolak</h4>
                            <p class="text-dark mb-4">Mohon maaf, pengajuan penarikan Anda belum dapat disetujui oleh Bendahara. Silakan periksa kembali alasan Anda dan coba lagi.</p>
                            <button onclick="tampilkanForm()" class="btn btn-danger px-4 fw-bold" {{ $isReadOnly ? 'disabled' : '' }}>Ajukan Ulang</button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- WADAH FORM TARIK --}}
            {{-- Form hanya otomatis tampil jika tidak ada pengajuan, atau pengajuan terakhir statusnya 'Batal' --}}
            <div id="form-tarik-container" style="display: {{ (!$pengajuan || $pengajuan->status == 'Batal') ? 'block' : 'none' }};">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        @if(session('success'))
                            <div id="alert" class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-1"></i> {{ session('alert-message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
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

                        <form action="{{ route('siswa.tabungan.ajukan') }}" method="POST">
                            @csrf
                            <h5 class="fw-bold text-primary mb-4">Form Tarik Tabungan Tunai/Digital</h5>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">ID / Username</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" value="{{ auth()->user()->username }}" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Nama Lengkap</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Rombel Aktif</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" value="{{ $rombel->name ?? 'Belum ada rombel' }}" readonly>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Tabungan Saat Ini</label>
                                <div class="col-sm-8">
                                    <div class="input-group mb-1">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control fw-bold" value="{{ number_format($nominal, 0, ',', '.') }}" readonly>
                                    </div>
                                    <small class="fst-italic text-muted">*{{ $terbilang }}</small>
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="jumlah_tarik" class="col-sm-4 col-form-label fw-bold">Jumlah Penarikan</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control form-control-lg" id="jumlah_tarik" name="jumlah_tarik" placeholder="Minimal 1.000" min="1000" max="{{ $nominal }}" required>
                                    </div>
                                    <small class="text-muted d-block mt-2">Biaya Admin (Estimasi): <span id="biaya_admin_text" class="fw-bold text-danger">Rp 0</span></small>
                                    <input type="hidden" id="biaya_admin" name="biaya_admin">
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="alasan" class="col-sm-4 col-form-label">Alasan Penarikan</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" name="alasan" id="alasan" rows="2" placeholder="Mengapa ingin menarik tabungan?" required></textarea>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="jenis_pembayaran" class="col-sm-4 col-form-label">Jenis Pembayaran</label>
                                <div class="col-sm-8">
                                    <select id="jenis_pembayaran" class="form-select" name="jenis_pembayaran" required>
                                        <option value="">Pilih Metode Pengambilan</option>
                                        <option value="Tunai">Tunai (Ambil di Sekolah)</option>
                                        <option value="Digital">Digital (Transfer Bank / E-Wallet)</option>
                                    </select>
                                </div>
                            </div>

                            <div id="digital-payment-details" style="display: none;" class="p-3 border bg-light rounded mb-4">
                                <h6 class="fw-bold text-primary mb-3">Informasi Tujuan Transfer</h6>
                                
                                <div class="mb-3 row">
                                    <label for="metode_digital" class="col-sm-4 col-form-label">Tipe Digital</label>
                                    <div class="col-sm-8">
                                        <select id="metode_digital" class="form-select" name="metode_digital">
                                            <option value="">Pilih Tipe</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="bank-transfer-details" style="display: none;">
                                    <div class="mb-3 row">
                                        <label for="bank_code" class="col-sm-4 col-form-label">Pilih Bank</label>
                                        <div class="col-sm-8">
                                            <select id="bank_code" class="form-select" name="bank_code">
                                                <option value="">Memuat data bank...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="nomor_rekening" class="col-sm-4 col-form-label">No. Rekening</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" placeholder="Contoh: 1234567890">
                                        </div>
                                    </div>
                                </div>

                                <div id="ewallet-details" style="display: none;">
                                    <div class="mb-3 row">
                                        <label for="ewallet_type" class="col-sm-4 col-form-label">Pilih E-Wallet</label>
                                        <div class="col-sm-8">
                                            <select id="ewallet_type" class="form-select" name="ewallet_type">
                                                <option value="">Memuat data e-wallet...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="ewallet_number" class="col-sm-4 col-form-label">No. Handphone</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="ewallet_number" name="ewallet_number" placeholder="Contoh: 081234567890">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <button class="btn btn-primary w-100 fw-bold" type="submit" {{ $isReadOnly ? 'disabled' : '' }}>
                                        <i class="bi bi-send me-1"></i> Ajukan Penarikan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-info-circle me-1"></i> Informasi Penting</h5>
                    <p class="mb-3 small">Penabung dapat mengajukan penarikan tabungan <strong>sebanyak 2x</strong> setiap semesternya dengan dua metode:</p>
                    
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item border-0 px-0">
                            <i class="bi bi-cash-coin text-success fs-5 me-2 align-middle"></i> <strong>Tunai</strong><br>
                            <small class="text-muted d-block mt-1">Pengambilan uang langsung di sekolah setelah disetujui.</small>
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="bi bi-wallet2 text-info fs-5 me-2 align-middle"></i> <strong>Digital</strong><br>
                            <small class="text-muted d-block mt-1">Dana ditransfer langsung secara otomatis ke rekening e-wallet/bank Anda.</small>
                        </li>
                    </ul>
                    
                    <div class="alert alert-warning mb-0 p-2 small" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Metode <strong>Digital</strong> mungkin dikenakan biaya layanan Rp 1.000 - Rp 5.000 oleh penyedia sistem gateway.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Batal --}}
<div class="modal fade" id="batalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger pb-3">
                <h5 class="modal-title text-white"><i class="bi bi-exclamation-octagon me-2"></i>Batalkan Pengajuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                Apakah Anda yakin ingin membatalkan antrean pengajuan penarikan tabungan ini?
            </div>
            <div class="modal-footer">
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
    // Fungsi ini dipanggil ketika user menekan tombol "Ajukan Ulang" atau "Tutup & Ajukan Baru"
    function tampilkanForm() {
        document.getElementById('status-card-container').style.display = 'none';
        document.getElementById('form-tarik-container').style.display = 'block';
    }
</script>
<script>
    // Menghilangkan alert otomatis
    @if(session('success') || session('error'))
        setTimeout(function() {
            let alertEl = document.getElementById('alert') || document.querySelector('.alert');
            if (alertEl) {
                let bsAlert = new bootstrap.Alert(alertEl);
                bsAlert.close();
            }
        }, 5000);
    @endif

    function genapkanKeRibuan(angka) {
        return Math.ceil(angka / 1000) * 1000;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const jumlahTarikInput = document.getElementById('jumlah_tarik');
        const biayaAdminText = document.getElementById('biaya_admin_text');
        const biayaAdminInput = document.getElementById('biaya_admin');

        if(jumlahTarikInput) {
            jumlahTarikInput.addEventListener('input', function () {
                let value = jumlahTarikInput.value.replace(/\D/g, '');
                let jumlahTarik = parseInt(value) || 0;

                let biaya = Math.ceil(jumlahTarik * 0.05);
                let dibulatkan = genapkanKeRibuan(biaya);

                biayaAdminText.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(dibulatkan);
                biayaAdminInput.value = dibulatkan;
            });
        }
    });

    function openBatalModal(id) {
        var form = document.getElementById('batalForm');
        form.action = '/siswa/tabungan/tarik/batal/' + id;
        var myModal = new bootstrap.Modal(document.getElementById('batalModal'));
        myModal.show();
    }
</script>

<script>
    // Logika form kondisional
    document.addEventListener('DOMContentLoaded', function() {
        const jenisPembayaran = document.getElementById('jenis_pembayaran');
        const digitalPaymentDetails = document.getElementById('digital-payment-details');
        const metodeDigital = document.getElementById('metode_digital');
        const bankTransferDetails = document.getElementById('bank-transfer-details');
        const ewalletDetails = document.getElementById('ewallet-details');

        if(jenisPembayaran) {
            jenisPembayaran.addEventListener('change', function() {
                digitalPaymentDetails.style.display = (this.value === 'Digital') ? 'block' : 'none';
                
                // Reset input jika ganti ke Tunai
                if(this.value !== 'Digital') {
                    metodeDigital.value = "";
                    bankTransferDetails.style.display = 'none';
                    ewalletDetails.style.display = 'none';
                }
            });

            metodeDigital.addEventListener('change', function() {
                bankTransferDetails.style.display = (this.value === 'bank_transfer') ? 'block' : 'none';
                ewalletDetails.style.display = (this.value === 'ewallet') ? 'block' : 'none';
            });

            // Fetch saluran payout dari server lu
            fetch("/payout-channels")
                .then(response => response.json())
                .then(data => {
                    populatePaymentOptions(data);
                })
                .catch(error => {
                    console.error("Error fetching payout channels:", error);
                    document.getElementById("bank_code").innerHTML = '<option value="">Gagal memuat data</option>';
                    document.getElementById("ewallet_type").innerHTML = '<option value="">Gagal memuat data</option>';
                });
        }
    });

    function populatePaymentOptions(channels) {
        let bankSelect = document.getElementById("bank_code");
        let ewalletSelect = document.getElementById("ewallet_type");
        let metodeDigitalSelect = document.getElementById("metode_digital");

        bankSelect.innerHTML = '<option value="">Pilih Bank</option>';
        ewalletSelect.innerHTML = '<option value="">Pilih E-Wallet</option>';
        metodeDigitalSelect.innerHTML = '<option value="">Pilih Metode Digital</option>';

        let addedCategories = { "BANK": false, "EWALLET": false };

        channels.forEach(channel => {
            let option = document.createElement("option");
            option.value = channel.channel_code;
            option.textContent = channel.channel_name;
            
            if (channel.channel_category === "BANK") {
                bankSelect.appendChild(option);
                addedCategories["BANK"] = true;
            } else if (channel.channel_category === "EWALLET") {
                ewalletSelect.appendChild(option);
                addedCategories["EWALLET"] = true;
            }
        });

        if (addedCategories["BANK"]) {
            let bankOption = document.createElement("option");
            bankOption.value = "bank_transfer";
            bankOption.textContent = "Transfer Bank";
            metodeDigitalSelect.appendChild(bankOption);
        }
        if (addedCategories["EWALLET"]) {
            let ewalletOption = document.createElement("option");
            ewalletOption.value = "ewallet";
            ewalletOption.textContent = "E-Wallet";
            metodeDigitalSelect.appendChild(ewalletOption);
        }
    }
</script>
@endsection