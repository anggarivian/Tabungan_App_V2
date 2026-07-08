@extends('layout.main')

@section('title') Export Data - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Export Data</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    @if(auth()->user()->roles_id == 1)
                        <li class="breadcrumb-item"><a href="{{ route ('kepsek.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 2)
                        <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 3)
                        <li class="breadcrumb-item"><a href="{{ route ('walikelas.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 4)
                        <li class="breadcrumb-item"><a href="{{ route ('siswa.dashboard')}}">Dashboard</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Export Data</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    {{-- Alert Info Periode Aktif --}}
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-info-circle-fill fs-5 me-2" style="margin-top: -17px;"></i>
        <div>
            <strong>Informasi:</strong> Seluruh data yang diexport otomatis disesuaikan dengan <strong>Periode Aktif</strong> saat ini.
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Navigation Tabs --}}
            <ul class="nav nav-tabs nav-tabs-custom" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="tabungan-tab" data-bs-toggle="tab" data-bs-target="#tabungan" type="button" role="tab" aria-controls="tabungan" aria-selected="true">
                        <i class="bi bi-wallet2 me-1"></i> Tabungan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi" type="button" role="tab" aria-controls="transaksi" aria-selected="false">
                        <i class="bi bi-arrow-left-right me-1"></i> Transaksi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="pengajuan-tab" data-bs-toggle="tab" data-bs-target="#pengajuan" type="button" role="tab" aria-controls="pengajuan" aria-selected="false">
                        <i class="bi bi-file-earmark-text me-1"></i> Pengajuan
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-4" id="myTabContent">
                {{-- TAB TABUNGAN --}}
                <div class="tab-pane fade show active" id="tabungan" role="tabpanel" aria-labelledby="tabungan-tab">
                    <form id="exportTabunganForm" action="{{ route('kepsek.export.tabungan') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pilih Jenis Export</label>
                                <div class="d-flex flex-column gap-2 mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_option" id="tabunganSiswa" value="siswa" checked>
                                        <label class="form-check-label" for="tabunganSiswa">Per Siswa</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_option" id="tabunganKelas" value="kelas">
                                        <label class="form-check-label" for="tabunganKelas">Per Kelas</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_option" id="tabunganSemua" value="semua">
                                        <label class="form-check-label" for="tabunganSemua">Semua Kelas</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-8 align-self-center">
                                <div id="siswaSection">
                                    <label for="siswaTabungan" class="form-label small fw-bold text-muted text-uppercase">Pilih Siswa</label>
                                    <select class="form-select" id="siswaTabungan" name="siswa_id">
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach($siswas as $siswa)
                                            <option value="{{ $siswa->id }}">{{ $siswa->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="kelasSection" style="display: none;">
                                    <label for="kelasTabungan" class="form-label small fw-bold text-muted text-uppercase">Pilih Kelas</label>
                                    <select class="form-select" id="kelasTabungan" name="kelas_id">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}">{{ $k->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="startDate" name="start_date">
                        <input type="hidden" id="endDate" name="end_date">

                        <div class="border-top mt-4 pt-3 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-danger px-4" name="export_type" value="pdf">
                                <i class="bi bi-file-earmark-pdf"></i> Export PDF
                            </button>
                            <button type="submit" class="btn btn-success px-4" name="export_type" value="excel">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- TAB TRANSAKSI --}}
                <div class="tab-pane fade" id="transaksi" role="tabpanel" aria-labelledby="transaksi-tab">
                    <form id="exportTransaksiForm" action="{{ route('kepsek.export.transaksi') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pilih Opsi Export</label>
                                <div class="d-flex flex-column gap-2 mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_option" id="transaksiSiswa" value="siswa" checked>
                                        <label class="form-check-label" for="transaksiSiswa">Per Siswa</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_option" id="transaksiKelas" value="kelas">
                                        <label class="form-check-label" for="transaksiKelas">Per Kelas</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_option" id="transaksiSemua" value="semua">
                                        <label class="form-check-label" for="transaksiSemua">Semua Kelas</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-4 align-self-center">
                                <div id="transaksiSiswaSelect">
                                    <label for="siswaTransaksi" class="form-label small fw-bold text-muted text-uppercase">Pilih Siswa</label>
                                    <select class="form-select" id="siswaTransaksi" name="siswa_id">
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach($siswas as $siswa)
                                            <option value="{{ $siswa->id }}">{{ $siswa->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="transaksiKelasSelect" style="display: none;">
                                    <label for="kelasTransaksi" class="form-label small fw-bold text-muted text-uppercase">Pilih Kelas</label>
                                    <select class="form-select" id="kelasTransaksi" name="kelas_id">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $kelasItem)
                                            <option value="{{ $kelasItem->id }}">{{ $kelasItem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-4 align-self-center">
                                <label for="dateRangeTransaksi" class="form-label small fw-bold text-muted text-uppercase">Rentang Tanggal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-calendar3" style="margin-top: -13px;"></i></span>
                                    <input type="text" class="form-control" id="dateRangeTransaksi" name="date_range" placeholder="Pilih Tanggal" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="border-top mt-4 pt-3 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-danger px-4" name="export_type" value="pdf">
                                <i class="bi bi-file-earmark-pdf"></i> Export PDF
                            </button>
                            <button type="submit" class="btn btn-success px-4" name="export_type" value="excel">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                        </div>
                    </form>
                </div>

                {{-- TAB PENGAJUAN --}}
                <div class="tab-pane fade" id="pengajuan" role="tabpanel" aria-labelledby="pengajuan-tab">
                    <form id="exportPengajuanForm" action="{{ route('kepsek.export.pengajuan') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Pilih Opsi Export</label>
                                <div class="d-flex flex-column gap-2 mt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_option" id="pengajuanSiswa" value="siswa" checked>
                                        <label class="form-check-label" for="pengajuanSiswa">Per Siswa</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_option" id="pengajuanKelas" value="kelas">
                                        <label class="form-check-label" for="pengajuanKelas">Per Kelas</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_option" id="pengajuanSemua" value="semua">
                                        <label class="form-check-label" for="pengajuanSemua">Semua Kelas</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-4 align-self-center">
                                <div id="pengajuanSiswaSelect">
                                    <label for="siswaPengajuan" class="form-label small fw-bold text-muted text-uppercase">Pilih Siswa</label>
                                    <select class="form-select" id="siswaPengajuan" name="siswa_id">
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach($siswas as $siswa)
                                            <option value="{{ $siswa->id }}">{{ $siswa->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="pengajuanKelasSelect" style="display: none;">
                                    <label for="kelasPengajuan" class="form-label small fw-bold text-muted text-uppercase">Pilih Kelas</label>
                                    <select class="form-select" id="kelasPengajuan" name="kelas_id">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas as $kelasItem)
                                            <option value="{{ $kelasItem->id }}">{{ $kelasItem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-4 align-self-center">
                                <label for="dateRangePengajuan" class="form-label small fw-bold text-muted text-uppercase">Rentang Tanggal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-calendar3" style="margin-top: -13px;"></i></span>
                                    <input type="text" class="form-control" id="dateRangePengajuan" name="date_range" placeholder="Pilih Tanggal" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="border-top mt-4 pt-3 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-danger px-4" name="export_type" value="pdf">
                                <i class="bi bi-file-earmark-pdf"></i> Export PDF
                            </button>
                            <button type="submit" class="btn btn-success px-4" name="export_type" value="excel">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Tabungan Form Toggle
        const tabunganSiswa = document.getElementById("tabunganSiswa");
        const tabunganKelas = document.getElementById("tabunganKelas");
        const tabunganSemua = document.getElementById("tabunganSemua");
        const siswaSection = document.getElementById("siswaSection");
        const kelasSection = document.getElementById("kelasSection");

        function updateTabunganForm() {
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
        tabunganSiswa.addEventListener("change", updateTabunganForm);
        tabunganKelas.addEventListener("change", updateTabunganForm);
        tabunganSemua.addEventListener("change", updateTabunganForm);

        // Transaksi Form Toggle
        const transaksiSiswa = document.getElementById("transaksiSiswa");
        const transaksiKelas = document.getElementById("transaksiKelas");
        const transaksiSemua = document.getElementById("transaksiSemua");
        const transaksiSiswaSelect = document.getElementById("transaksiSiswaSelect");
        const transaksiKelasSelect = document.getElementById("transaksiKelasSelect");

        function updateTransaksiForm() {
            if (transaksiSiswa.checked) {
                transaksiSiswaSelect.style.display = "block";
                transaksiKelasSelect.style.display = "none";
            } else if (transaksiKelas.checked) {
                transaksiSiswaSelect.style.display = "none";
                transaksiKelasSelect.style.display = "block";
            } else {
                transaksiSiswaSelect.style.display = "none";
                transaksiKelasSelect.style.display = "none";
            }
        }
        transaksiSiswa.addEventListener("change", updateTransaksiForm);
        transaksiKelas.addEventListener("change", updateTransaksiForm);
        transaksiSemua.addEventListener("change", updateTransaksiForm);

        // Pengajuan Form Toggle
        const pengajuanSiswa = document.getElementById("pengajuanSiswa");
        const pengajuanKelas = document.getElementById("pengajuanKelas");
        const pengajuanSemua = document.getElementById("pengajuanSemua");
        const pengajuanSiswaSelect = document.getElementById("pengajuanSiswaSelect");
        const pengajuanKelasSelect = document.getElementById("pengajuanKelasSelect");

        function updatePengajuanForm() {
            if (pengajuanSiswa.checked) {
                pengajuanSiswaSelect.style.display = "block";
                pengajuanKelasSelect.style.display = "none";
            } else if (pengajuanKelas.checked) {
                pengajuanSiswaSelect.style.display = "none";
                pengajuanKelasSelect.style.display = "block";
            } else {
                pengajuanSiswaSelect.style.display = "none";
                pengajuanKelasSelect.style.display = "none";
            }
        }
        pengajuanSiswa.addEventListener("change", updatePengajuanForm);
        pengajuanKelas.addEventListener("change", updatePengajuanForm);
        pengajuanSemua.addEventListener("change", updatePengajuanForm);

        // Inisialisasi awal pemilih form
        updateTabunganForm();
        updateTransaksiForm();
        updatePengajuanForm();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let isProcessing = false;

        document.querySelectorAll('button[name="export_type"]').forEach(button => {
            button.addEventListener("click", function() {
                isProcessing = true;
                setTimeout(() => { isProcessing = false; }, 5000);

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

    new Litepicker({
        element: document.getElementById('dateRangePengajuan'),
        format: 'YYYY-MM-DD',
        singleMode: false,
        autoApply: true,
        allowRepick: true,
    });
</script>
@endsection
