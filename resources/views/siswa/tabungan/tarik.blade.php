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
    <div class="row">
        <div class="col-12 col-sm-12 col-md-8 col-lg-8">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    @if(session('success'))
                        <div id="alert" class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
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

                    @if($pengajuan && $pengajuan->status == 'Pending')
                        <h5 class="fw-bold mb-3 text-primary">Menunggu Proses Bendahara</h5>
                        <p>Detail Pengajuan Penarikan Tabungan :</p>
                        <div class="success-details">
                            <table class="table table-borderless mb-0">
                                @php
                                    $jumlahTarik = $pengajuan->jumlah_penarikan;
                                    $biayaAdmin = ceil(($jumlahTarik * 0.05) / 1000) * 1000;
                                @endphp
                                <tr>
                                    <th>ID Tabungan</th>
                                    <td>{{ $pengajuan->user->username }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah Penarikan</th>
                                    <td>Rp. {{ number_format($pengajuan->jumlah_penarikan, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Biaya Admin (5%)<br><small class="text-muted">*Belum termasuk biaya gateway digital</small></th>
                                    <td>Rp. {{ number_format($biayaAdmin, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Pembayaran</th>
                                    <td>
                                        <span class="badge {{ $pengajuan->pembayaran == 'Tunai' ? 'bg-success' : 'bg-primary' }}">
                                            {{ $pengajuan->pembayaran }}
                                        </span>
                                    </td>
                                </tr>
                                @if($pengajuan->pembayaran == 'Digital')
                                    <tr>
                                        <th>Jalur Transaksi</th>
                                        <td>{{ $pengajuan->metode_digital == 'bank_transfer' ? 'Transfer Bank' : 'E-Wallet' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Penyedia Layanan</th>
                                        <td>{{ $pengajuan->metode_digital == 'bank_transfer' ? $pengajuan->bank_code : $pengajuan->ewallet_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nomor Tujuan</th>
                                        <td class="fw-bold">{{ $pengajuan->metode_digital == 'bank_transfer' ? $pengajuan->nomor_rekening : $pengajuan->ewallet_number }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Alasan</th>
                                    <td>{{ $pengajuan->alasan }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Pada</th>
                                    <td>{{ \Carbon\Carbon::parse($pengajuan->created_at)->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <button class="btn btn-danger w-100 fw-bold" onclick="openBatalModal({{ $pengajuan->id }})">
                            <i class="bi bi-x-circle me-1"></i> Batalkan Pengajuan
                        </button>
                    @else
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
                                        <input type="text" class="form-control fw-bold" value="{{ number_format($saldoSaatIni, 0, ',', '.') }}" readonly>
                                    </div>
                                    <small class="fst-italic text-muted">*{{ $terbilang }}</small>
                                </div>
                            </div>

                            <div class="mb-4 row">
                                <label for="jumlah_tarik" class="col-sm-4 col-form-label fw-bold">Jumlah Penarikan</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control form-control-lg" id="jumlah_tarik" name="jumlah_tarik" placeholder="Minimal 1.000" min="1000" max="{{ $saldoSaatIni }}" required>
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

                            <div id="digital-payment-details" style="display: none;" class="p-3 border rounded mb-4">
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
                                    <button class="btn btn-primary w-100 fw-bold" type="submit">
                                        <i class="bi bi-send me-1"></i> Ajukan Penarikan
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
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