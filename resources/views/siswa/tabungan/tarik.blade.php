@extends('layout.main')

@section('title') Ajukan Tarik Tabungan - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-2">Ajukan Tarik Tabungan</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    @if(auth()->user()->roles_id == 1)
                        <li class="breadcrumb-item"><a href="{{ route('kepsek.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 2)
                        <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 3)
                        <li class="breadcrumb-item"><a href="{{ route('walikelas.bendahara') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 4)
                        <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Pengajuan Tarik Tabungan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="row">
        <!-- Card Form Penarikan -->
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
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
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
                    @php
                        $userId = auth()->user()->id;
                        $pengajuan = \App\Models\Pengajuan::where('user_id', $userId)->latest()->first();
                    @endphp

                    @if($pengajuan && $pengajuan->status == 'Pending')
                        <h5 class="fw-bold mb-3 text-primary">Menunggu Proses Bendahara</h5>
                        <p>Detail Pengajuan Penarikan Tabungan :</p>
                        <table class="table table-borderless">
                            <tr>
                                <th>ID Tabungan</th>
                                <td>{{ $pengajuan->user->username }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah Penarikan</th>
                                <td>Rp{{ number_format($pengajuan->jumlah_penarikan, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Pembayaran</th>
                                <td>{{ $pengajuan->pembayaran }}</td>
                            </tr>
                            <tr>
                                <th>Metode Digital</th>
                                <td>{{ $pengajuan->metode_digital }}</td>
                            </tr>
                            <tr>
                                <th>E-Wallet</th>
                                <td>{{ $pengajuan->ewallet_type }}</td>
                            </tr>
                            <tr>
                                <th>Nomor E-Wallet</th>
                                <td>{{ $pengajuan->ewallet_number }}</td>
                            </tr>
                            <tr>
                                <th>Alasan</th>
                                <td>{{ $pengajuan->alasan }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>{{ \Carbon\Carbon::parse($pengajuan->created_at)->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                        <button class="btn btn-danger w-100" onclick="openBatalModal({{ $pengajuan->id }})">
                            Batalkan Pengajuan
                        </button>
                    @else
                        <form action="{{ route('siswa.tabungan.ajukan') }}" method="POST">
                            @csrf
                            <h5 class="fw-bold text-primary mb-3">
                                Tarik Tabungan Tunai/Digital
                            </h5>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <label for="id">ID Tabungan</label>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                        <input type="text" class="form-control" id="id" name="id" value="{{ auth()->user()->username }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <label for="nama">Nama</label>
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
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" id="jumlah_tabungan" name="jumlah_tabungan" value="{{ auth()->user()->tabungan->saldo }}" readonly>
                                        </div>
                                        <p class="fst-italic fw-lighter text-start mb-0">*{{$terbilang}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <label for="jumlah_tarik">Jumlah Pengajuan Penarikan</label>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="number" class="form-control" id="jumlah_tarik" name="jumlah_tarik" placeholder="Masukkan Jumlah Penarikan" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <label for="alasan">Alasan</label>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                        <textarea class="form-control" name="alasan" id="alasan" rows="2" placeholder="Mengapa ingin melakukan penarikan tabungan ..." required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                        <label for="jenis_pembayaran">Jenis Pembayaran</label>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                        <select id="jenis_pembayaran" class="form-select" name="jenis_pembayaran" required>
                                            <option value="">Pilih Jenis Pembayaran</option>
                                            <option value="Tunai">Tunai</option>
                                            <option value="Digital">Digital</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Detail Pembayaran Digital (ditampilkan kondisional) -->
                            <div id="digital-payment-details" style="display: none;">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                            <label for="metode_digital">Metode Digital</label>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                            <select id="metode_digital" class="form-select" name="metode_digital">
                                                <option value="">Pilih Metode Digital</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Transfer Bank -->
                                <div id="bank-transfer-details" style="display: none;">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                                <label for="bank_code">Bank</label>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                                <select id="bank_code" class="form-select" name="bank_code">
                                                    <option value="">Pilih Bank</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                                <label for="nomor_rekening">Nomor Rekening</label>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                                <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" placeholder="Masukkan Nomor Rekening">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail E-Wallet -->
                                <div id="ewallet-details" style="display: none;">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                                <label for="ewallet_type">E-Wallet</label>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                                <select id="ewallet_type" class="form-select" name="ewallet_type">
                                                    <option value="">Pilih E-Wallet</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                                <label for="ewallet_number">Nomor E-Wallet</label>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                                <input type="text" class="form-control" id="ewallet_number" name="ewallet_number" placeholder="Masukkan Nomor E-Wallet">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-primary w-100">Ajukan</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card Informasi Penting -->
        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="bi bi-info-circle"></i> Informasi Penting
                    </h5>
                    <p class="mb-2">Penabung dapat mengajukan penarikan tabungan melalui bendahara dengan dua metode:</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0 px-0">
                            <i class="bi bi-cash-coin text-success"></i> <strong>Tunai</strong><br>
                            <small class="text-muted">Pengambilan uang langsung di sekolah</small>
                        </li>
                        <li class="list-group-item border-0 px-0">
                            <i class="bi bi-wallet2 text-info"></i> <strong>Digital</strong><br>
                            <small class="text-muted">Dana ditransfer langsung ke e-wallet/bank yang Anda pilih</small>
                        </li>
                    </ul>
                    <div class="alert alert-warning mt-3" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <small>Perhatikan bahwa metode <strong>Digital</strong> memiliki biaya administrasi yang berbeda.</small>
                    </div>
                </div>
            </div>
        </div>
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
        form.action = '/siswa/tabungan/tarik/batal/' + id;

        var myModal = new bootstrap.Modal(document.getElementById('batalModal'));
        myModal.show();
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenisPembayaran = document.getElementById('jenis_pembayaran');
        const digitalPaymentDetails = document.getElementById('digital-payment-details');
        const metodeDigital = document.getElementById('metode_digital');
        const bankTransferDetails = document.getElementById('bank-transfer-details');
        const ewalletDetails = document.getElementById('ewallet-details');

        jenisPembayaran.addEventListener('change', function() {
            digitalPaymentDetails.style.display = (this.value === 'Digital') ? 'block' : 'none';
        });

        metodeDigital.addEventListener('change', function() {
            bankTransferDetails.style.display = (this.value === 'bank_transfer') ? 'block' : 'none';
            ewalletDetails.style.display = (this.value === 'ewallet') ? 'block' : 'none';
        });

        fetch("/payout-channels")
            .then(response => response.json())
            .then(data => {
                populatePaymentOptions(data);
            })
            .catch(error => console.error("Error fetching payout channels:", error));
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
