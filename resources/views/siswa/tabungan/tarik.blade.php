@extends('layout.main')

@section('title') Ajukan Tarik Tabungan - SakuRame @endsection

@section('content')
<div class="page-heading mb-2">
    <div class="d-flex justify-content-between">
        <h3 class="mt-3">Ajukan Tarik Tabungan</h3>
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
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Tarik Tabungan</li>
            </ol>
        </nav>
    </div>
</div>
<div class="page-content">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
            <div class="card mb-3">
                <div class="card-body">
                        <h5>Informasi Penting</h5>
                        <p>Penabung dapat melakukan pengajuaan penarikan tabungan ke bendahara</p>
                        <p>- Tunai untuk pengambilan uang cash langsung ke sekolah</p>
                        <p>- Digital untuk pengambilan yang langsung di transfer ke wallet yang anda pilih</p>
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
                    @php
                        $userId = auth()->user()->id;
                        $pengajuan = \App\Models\Pengajuan::where('user_id', $userId)->latest()->first();
                    @endphp

                    @if($pengajuan && $pengajuan->status == 'Pending')
                        <div class="container">
                            <p class="mt-2">Pengajuan kamu sedang diproses oleh bendahara !!!</p>
                        </div>
                    @else
                        <form action="{{ route('siswa.tabungan.ajukan') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="id">ID Tabungan</label>
                                <input type="text" class="form-control" id="id" name="id" value="{{ auth()->user()->username }}" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="kelas">Kelas</label>
                                <input type="text" class="form-control" id="kelas" name="kelas" value="{{ auth()->user()->kelas->name }}" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="jumlah_tabungan">Tabungan Saat Ini</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="number" class="form-control" id="jumlah_tabungan" name="jumlah_tabungan" value="{{ auth()->user()->tabungan->saldo }}" readonly>
                                </div>
                                <p class="fst-italic fw-lighter text-center mb-0">{{ $terbilang ?? '' }}</p>
                            </div>
                            <div class="form-group mb-3">
                                <label for="jumlah_tarik">Jumlah Pengajuan Penarikan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp.</span>
                                    <input type="number" class="form-control" id="jumlah_tarik" name="jumlah_tarik" placeholder="Masukkan Jumlah Penarikan" required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="alasan">Alasan</label>
                                <textarea class="form-control" name="alasan" id="alasan" rows="2" placeholder="Mengapa ingin melakukan penarikan tabungan ..." required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="jenis_pembayaran">Jenis Pembayaran</label>
                                <select id="jenis_pembayaran" class="form-select" name="jenis_pembayaran" required>
                                    <option value="">Pilih Jenis Pembayaran</option>
                                    <option value="Tunai">Tunai</option>
                                    <option value="Digital">Digital</option>
                                </select>
                            </div>

                            <!-- Detail Pembayaran Digital (ditampilkan kondisional) -->
                            <div id="digital-payment-details" style="display: none;">
                                <div class="form-group mb-3">
                                    <label for="metode_digital">Metode Digital</label>
                                    <select id="metode_digital" class="form-select" name="metode_digital">
                                        <option value="">Pilih Metode Digital</option>
                                    </select>
                                </div>

                                <!-- Detail Transfer Bank -->
                                <div id="bank-transfer-details" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label for="bank_code">Bank</label>
                                        <select id="bank_code" class="form-select" name="bank_code">
                                            <option value="">Pilih Bank</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="nomor_rekening">Nomor Rekening</label>
                                        <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" placeholder="Masukkan Nomor Rekening">
                                    </div>
                                </div>

                                <!-- Detail E-Wallet -->
                                <div id="ewallet-details" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label for="ewallet_type">E-Wallet</label>
                                        <select id="ewallet_type" class="form-select" name="ewallet_type">
                                            <option value="">Pilih E-Wallet</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="ewallet_number">Nomor E-Wallet</label>
                                        <input type="text" class="form-control" id="ewallet_number" name="ewallet_number" placeholder="Masukkan Nomor E-Wallet">
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
    </div>
</div>
@endsection

@section('js')
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
