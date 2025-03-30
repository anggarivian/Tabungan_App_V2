@extends('layout.main')

@section('title') Laporan Pengajuan - SakuRame @endsection

@section('content')
<div class="page-heading mb-2">
    <div class="d-flex justify-content-between ">
        <h3 class="mt-3">Laporan Pengajuan</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-right">
                @if(auth()->user()->roles_id == 1)
                    <li class="breadcrumb-item"><a href="{{ route ('kepsek.dashboard')}}">Dashboard</a></li>
                @elseif(auth()->user()->roles_id == 2)
                    <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                @elseif(auth()->user()->roles_id == 3)
                    <li class="breadcrumb-item"><a href="{{ route ('walikelas.dashboard')}}">Dashboard</a></li>
                @elseif(auth()->user()->roles_id == 4)
                    <li class="breadcrumb-item"><a href="{{ route ('siswa.dashboard')}}">Dashboard</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">Laporan Pengajuan</li>
            </ol>
        </nav>
    </div>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="page-content">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tabungan-tab" data-bs-toggle="tab" data-bs-target="#tabungan" type="button" role="tab" aria-controls="tabungan" aria-selected="true">Tabungan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi" type="button" role="tab" aria-controls="transaksi" aria-selected="false">Transaksi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pengajuan-tab" data-bs-toggle="tab" data-bs-target="#pengajuan" type="button" role="tab" aria-controls="pengajuan" aria-selected="false">Pengajuan</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tabungan" role="tabpanel" aria-labelledby="tabungan-tab">
                        <div class="mt-3">
                            <form id="exportTabunganForm" action="{{ route('siswa.export.tabungan') }}" method="POST">
                                @csrf
                                <input type="hidden" name="siswa_id" value="{{ auth()->user()->id }}">
                                <label class="form-label mb-0">Pilih Opsi Export</label>
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary" name="export_type" value="pdf">Export PDF</button>
                                        <button type="submit" class="btn btn-success" name="export_type" value="excel">Export Excel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="transaksi" role="tabpanel" aria-labelledby="transaksi-tab">
                        <div class="mt-3">
                            <form id="exportTransaksiForm" action="{{ route('siswa.export.transaksi') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="dateRangeTransaksi" class="form-label">Pilih Rentang Tanggal</label>
                                        <input type="text" class="form-control" id="dateRangeTransaksi" name="date_range" placeholder="Pilih Tanggal" autocomplete="off">
                                    </div>
                                </div>
                                <input type="hidden" name="siswa_id" value="{{ auth()->user()->id }}">
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary" name="export_type" value="pdf">Export PDF</button>
                                        <button type="submit" class="btn btn-success" name="export_type" value="excel">Export Excel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pengajuan" role="tabpanel" aria-labelledby="pengajuan-tab">
                        <div class="mt-3">
                            <form id="exportPengajuanForm" action="{{ route('siswa.export.pengajuan') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="dateRangePengajuan" class="form-label">Pilih Rentang Tanggal</label>
                                        <input type="text" class="form-control" id="dateRangePengajuan" name="date_range" placeholder="Pilih Tanggal" autocomplete="off">
                                    </div>
                                </div>
                                <input type="hidden" name="siswa_id" value="{{ auth()->user()->id }}">
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary" name="export_type" value="pdf">Export PDF</button>
                                        <button type="submit" class="btn btn-success" name="export_type" value="excel">Export Excel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tabunganSiswa = document.getElementById("tabunganSiswa");
        const tabunganKelas = document.getElementById("tabunganKelas");
        const tabunganSemua = document.getElementById("tabunganSemua");
        const siswaSection = document.getElementById("siswaSection");
        const kelasSection = document.getElementById("kelasSection");

        function updateForm() {
            if (tabunganSiswa.checked) {
                siswaSection.style.display = "block";
                kelasSection.style.display = "none";
            } else if (tabunganKelas.checked) {
                siswaSection.style.display = "none";
                kelasSection.style.display = "block";
            } else {
                siswaSection.style.display = "none";
                kelasSection.style.display = "none";
            }
        }

        tabunganSiswa.addEventListener("change", updateForm);
        tabunganKelas.addEventListener("change", updateForm);
        tabunganSemua.addEventListener("change", updateForm);
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const siswaOption = document.getElementById("transaksiSiswa");
        const kelasOption = document.getElementById("transaksiKelas");
        const semuaOption = document.getElementById("transaksiSemua");

        const siswaContainer = document.getElementById("transaksiSiswaSelect");
        const kelasContainer = document.getElementById("transaksiKelasSelect");

        function toggleFields() {
            if (siswaOption.checked) {
                siswaContainer.style.display = "block";
                kelasContainer.style.display = "none";
            } else if (kelasOption.checked) {
                siswaContainer.style.display = "none";
                kelasContainer.style.display = "block";
            } else {
                siswaContainer.style.display = "none";
                kelasContainer.style.display = "none";
            }
        }

        siswaOption.addEventListener("change", toggleFields);
        kelasOption.addEventListener("change", toggleFields);
        semuaOption.addEventListener("change", toggleFields);

        toggleFields();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const siswaOption = document.getElementById("pengajuanSiswa");
        const kelasOption = document.getElementById("pengajuanKelas");
        const semuaOption = document.getElementById("pengajuanSemua");

        const siswaContainer = document.getElementById("pengajuanSiswaSelect");
        const kelasContainer = document.getElementById("pengajuanKelasSelect");

        function toggleFields() {
            if (siswaOption.checked) {
                siswaContainer.style.display = "block";
                kelasContainer.style.display = "none";
            } else if (kelasOption.checked) {
                siswaContainer.style.display = "none";
                kelasContainer.style.display = "block";
            } else {
                siswaContainer.style.display = "none";
                kelasContainer.style.display = "none";
            }
        }

        siswaOption.addEventListener("change", toggleFields);
        kelasOption.addEventListener("change", toggleFields);
        semuaOption.addEventListener("change", toggleFields);

        toggleFields();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let isProcessing = false;

        document.querySelectorAll('button[name="export_type"]').forEach(button => {
            button.addEventListener("click", function() {
                isProcessing = true;

                setTimeout(() => {
                    isProcessing = false;
                }, 5000);

                const checkAndReload = setInterval(() => {
                    if (!isProcessing) {
                        clearInterval(checkAndReload);
                        location.reload();
                    }
                }, 500);
            });
        });
    });
</script>

<script>
    new Litepicker({
        element: document.getElementById('dateRangeTransaksi'),
        format: 'YYYY-MM-DD',
        singleMode: false,
        autoApply: true,
        allowRepick: true,
    });
</script>

<script>
    new Litepicker({
        element: document.getElementById('dateRangePengajuan'),
        format: 'YYYY-MM-DD',
        singleMode: false,
        autoApply: true,
        allowRepick: true,
    });
</script>

@endsection
