@extends('layout.main')

@section('title', 'Tabungan - SakuRame')

@section('content')
<style>
    /* Styling Animasi Teks Header */
    .page-heading h3 {
        font-size: 1.8rem;
        background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: gradientMove 3s ease-in-out infinite;
        font-weight: 800;
        margin-bottom: 0.2rem;
    }

    .disabled-link{
        pointer-events: none;
        opacity: .5;
        cursor: not-allowed;
    }

    .page-heading h4 {
        font-size: 1.1rem;
        color: #6c757d;
        font-weight: 400;
        margin-bottom: 0;
    }

    @keyframes gradientMove {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

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

    .icon-box {
        width: 54px;
        height: 54px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .disabled-link{
        pointer-events: none;
        opacity: .5;
        cursor: not-allowed;
    }

    .icon-box svg {
        width: 28px;
        height: 28px;
    }

    /* Custom Colors for Icons */
    .bg-blue-light { background-color: #e0f2fe; color: #0284c7; }
    .bg-green-light { background-color: #dcfce7; color: #16a34a; }
    .bg-purple-light { background-color: #f3e8ff; color: #9333ea; }
    .bg-red-light { background-color: #fee2e2; color: #dc2626; }
    .bg-orange-light { background-color: #ffedd5; color: #ea580c; }
    
    /* Custom Tabs */
    .custom-tabs .nav-link {
        color: #6c757d;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.75rem 1.5rem;
    }
    
    .custom-tabs .nav-link.active {
        color: #764ba2;
        border-bottom-color: #764ba2;
        background: transparent;
    }
</style>

<div class="page-heading mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="m-0 fw-bold text-dark">Informasi Tabungan</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <span class="badge text-secondary border px-3 py-2 fs-6 fw-medium shadow-sm">
                <i class="bi bi-calendar3 me-2"></i>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
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
    
    <!-- Section Kartu Statistik -->
    <div class="row g-3 mb-3">
        <!-- Card Uang Masuk & Keluar (Digabung biar lebih proporsional) -->

        <div class="col-12 col-md-6 col-xl-6">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-green-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/></svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Transaksi Masuk</h6>
                        <h5 class="font-extrabold mb-1">Rp {{ number_format($transaksi_masuk) }}</h5>
                        <small class="text-success fw-bold"><i class="bi bi-arrow-up-circle"></i> {{$storKali}}x Setoran</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-6">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-red-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4z"/></svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Transaksi Keluar</h6>
                        <h5 class="font-extrabold mb-1">Rp {{ number_format($transaksi_keluar) }}</h5>
                        <small class="text-danger fw-bold"><i class="bi bi-arrow-down-circle"></i> {{$tarikKali}}x Penarikan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Input Aksi --}}
    {{-- Tambahkan position-relative dan z-index tinggi di bungkus paling luarnya --}}
    <div class="row mb-3 position-relative" style="z-index: 999;">
        <div class="col-12">
            {{-- Tambahkan overflow: visible biar dropdown gak kepotong sama border radius card --}}
            <div class="card stat-card mb-0 shadow-sm border-0" style="overflow: visible;">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3"><i class="bi bi-grid me-2"></i>Menu Transaksi Cepat</h5>
                    
                    <div class="d-flex flex-column flex-md-row gap-3">
                        
                        <div class="flex-fill">
                            <a href="{{ route('bendahara.tabungan.stor') }}" class="btn {{ $isReadOnly ? 'disabled-link' : '' }} btn-primary w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" {{ $isReadOnly ? 'disabled' : '' }}>
                                <i class="bi bi-person-plus-fill fs-5" style="margin-top: -18px; margin-left: -5px;"></i>
                                <span>Stor Per Orang</span>
                            </a>
                        </div>

                        <div class="flex-fill position-relative">
                            <div class="dropdown h-100">
                                <button {{ $isReadOnly ? 'disabled' : '' }} class="btn btn-primary dropdown-toggle w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-people-fill fs-5" style="margin-top: -18px; margin-left: -5px;"></i>
                                    Stor Per Rombel
                                </button>
                                @if($isReadOnly)
                                    <ul class="dropdown-menu w-100 shadow" style="max-height: 250px; overflow-y: auto;">
                                        <li><span class="dropdown-item text-muted" style="margin-top: -18px; margin-left: -5px;">Tidak bisa melakukan stor rombel</span></li>
                                    </ul>
                                @else
                                <ul class="dropdown-menu w-100 shadow" style="max-height: 250px; overflow-y: auto;">
                                    @forelse($rombels as $r)
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ url('/bendahara/tabungan/stor/rombel/' . $r->id) }}">
                                                <i class="bi bi-box-arrow-in-right me-2 text-muted" style="margin-top: -18px; margin-left: -5px;"></i>{{ $r->name }}
                                            </a>
                                        </li>
                                    @empty
                                        <li><span class="dropdown-item text-muted" style="margin-top: -18px; margin-left: -5px;">Belum ada rombel</span></li>
                                    @endforelse
                                @endif
                                </ul>
                            </div>
                        </div>

                        <div class="flex-fill">
                            <a href="{{ route('bendahara.tabungan.tarik') }}" class="btn {{ $isReadOnly ? 'disabled-link' : '' }} btn-danger w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" {{ $isReadOnly ? 'disabled' : '' }}>
                                <i class="bi bi-cash-coin fs-5" style="margin-top: -18px; margin-left: -5px;"></i>
                                <span>Tarik Tunai</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section Tabel Transaksi Harian (Desain Baru) --}}
    <div class="card stat-card shadow-sm border-0">
        {{-- Hapus bg-white, biarkan class card-header bawaan template bekerja --}}
        <div class="card-header pt-4 pb-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="card-title mb-0"><i class="bi bi-journal-text me-2"></i>Rekap Transaksi Harian</h5>
            
            <div class="d-flex align-items-center gap-2">
                <label for="selectRombel" class="text-muted small fw-medium text-nowrap">Pilih Rombel:</label>
                <select id="selectRombel" class="form-select" style="min-width: 200px;" onchange="showTable(this.value)">
                    @foreach($rombels as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>

                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#rekapRombelModal">
                    <i class="bi bi-table me-1"></i> Rekap Semua Rombel
                </button>
            </div>
        </div>
        
        <div class="card-body pt-4">
            <div class="dropdown-table w-100">
                @foreach($rombels as $r)
                    @php $rombelData = $rombelList[$r->id]; @endphp
                    <div id="table{{ $r->id }}" class="rombel-table-container table-responsive" style="display: none;">
                        
                        {{-- Mini Dashboard Rombel (Tanpa bg-light, lebih bersih) --}}
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-2">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-bookmark-fill text-primary me-2"></i>Data {{ $rombelData['nama_rombel'] }}
                            </h6>
                            <div class="d-flex gap-2">
                                {{-- Menggunakan badge standar agar warnanya solid dan aman di dark mode --}}
                                <span class="badge bg-success px-3 py-2">
                                    <i class="bi bi-arrow-down-left-circle me-1"></i> Stor: Rp {{ number_format($rombelData['stor']) }}
                                </span>
                                <span class="badge bg-danger px-3 py-2">
                                    <i class="bi bi-arrow-up-right-circle me-1"></i> Tarik: Rp {{ number_format($rombelData['tarik']) }}
                                </span>
                            </div>
                        </div>

                        <table class="table table-hover align-middle mb-0" style="width: 100%">
                            {{-- Hapus table-light agar header tabel menyatu dengan warna card di dark mode --}}
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <!-- <th class="text-center">Kode</th> -->
                                    <th>Nama Siswa</th>
                                    {{-- Pastikan header angka rata kanan (text-end) presisi dengan isinya --}}
                                    <th class="text-end">Saldo Awal</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Saldo Akhir</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Pembuat</th>
                                    <th class="text-center">Waktu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rombelData['data'] as $transaksi)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <!-- <td class="text-center">
                                            <span class="badge bg-secondary">{{ $transaksi->user->username }}</span>
                                        </td> -->
                                        <td class="fw-medium">{{ Str::limit($transaksi->user->name, 25, '...') }}</td>
                                        <td class="text-end text-muted">Rp {{ number_format($transaksi->saldo_awal ?? 0) }}</td>
                                        <td class="text-end fw-medium {{ $transaksi->tipe_transaksi == 'Stor' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaksi->tipe_transaksi == 'Stor' ? '+' : '-' }} Rp {{ number_format($transaksi->jumlah_transaksi ?? 0) }}
                                        </td>
                                        <td class="text-end fw-bold">Rp {{ number_format($transaksi->saldo_akhir ?? 0) }}</td>
                                        <td class="text-center">
                                            @if ($transaksi->tipe_transaksi == 'Stor')
                                                <span class="badge bg-success">Stor | {{ $transaksi->pembayaran ?? '-' }}</span>
                                            @elseif ($transaksi->tipe_transaksi == 'Tarik')
                                                <span class="badge bg-danger">Tarik | {{ $transaksi->pembayaran ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center small">{{ Str::limit($transaksi->pembuat, 15, '...') }}</td>
                                        <td class="text-center small text-muted">{{ \Carbon\Carbon::parse($transaksi->created_at)->format('H:i') }}</td>
                                        <td class="text-center">
                                            {{-- Tombol untuk trigger Modal --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2" 
                                                    onclick="batalTransaksi({{ $transaksi->id }}, '{{ addslashes($transaksi->user->name) }}', {{ $transaksi->jumlah_transaksi }}, '{{ $transaksi->tipe_transaksi }}')">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="bi bi-journal-x display-4 text-muted d-block mb-3"></i>
                                            <h6 class="text-muted fw-normal">Belum ada transaksi di rombel ini hari ini.</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Modal Batal Transaksi --}}
<div class="modal fade" id="batalModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger pt-4 pb-3">
                <h5 class="modal-title text-white"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Batal Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formBatal" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-warning py-2 small mb-4">
                        <i class="bi bi-info-circle me-1"></i> Membatalkan transaksi akan otomatis mengurangi/menambah saldo akhir siswa. Data yang dibatalkan tidak bisa dikembalikan.
                    </div>
                    
                    <p class="mb-4 text-center">
                        Batalkan transaksi <strong id="batalTipe" class="text-uppercase"></strong> sebesar <br>
                        <strong id="batalNominal" class="fs-4 text-danger"></strong><br>
                        atas nama <strong id="batalNama"></strong>?
                    </p>
                    
                    <div class="mb-2 bg-body-secondary p-3 rounded border">
                        <label class="form-label fw-bold text-center w-100 mb-2">Ketik "BATAL" untuk konfirmasi:</label>
                        <input type="text" name="konfirmasi" class="form-control text-center text-uppercase fw-bold" placeholder="BATAL" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger px-4">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="rekapRombelModal" tabindex="-1" aria-labelledby="rekapRombelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary" id="rekapRombelModalLabel">
                    <i class="bi bi-bar-chart-line me-2"></i> Rekap Setoran Harian Per Rombel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0" style="min-width: 1000px;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No.</th>
                                <th style="width: 120px;">Tanggal</th>
                                @foreach ($rombels as $r)
                                    <th class="text-end text-nowrap">{{ $r->name }}</th>
                                @endforeach
                                <th class="text-end fw-bold">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekapRombel as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d M Y') }}</td>
                                    @foreach ($rombels as $r)
                                        @php $nominal = $row['rombel_' . $r->id] ?? 0; @endphp
                                        <td class="text-end">{{ $nominal == 0 ? '0' : number_format($nominal, 0, ',', '.') }}</td>
                                    @endforeach
                                    <td class="text-end fw-bold text-success">{{ number_format($row['total_semua'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $rombels->count() + 3 }}" class="text-center py-4">Belum ada data transaksi setoran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function loadRekapRombel() {
        const thead = document.getElementById('rekapRombelHead');
        const tbody = document.getElementById('rekapRombelBody');

        // Tampilkan loading state
        thead.innerHTML = '';
        tbody.innerHTML = '<tr><td class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Memuat data...</td></tr>';

        // Ambil periode_id yang sedang aktif di filter halaman (kalau ada)
        const params = new URLSearchParams(window.location.search);
        const periodeId = params.get('periode_id') || document.getElementById('periode_id')?.value || '';

        let url = '{{ route('bendahara.rekap.rombel.data') }}';
        if (periodeId) {
            url += '?periode_id=' + encodeURIComponent(periodeId);
        }

        fetch(url, {
            cache: 'no-store',
            headers: {
                'Cache-Control': 'no-cache',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                // 1. Build Thead (Kolom)
                let headHtml = '<tr><th class="text-center" style="width: 50px;">No.</th><th style="width: 120px;">Tanggal</th>';
                data.rombels.forEach(r => {
                    headHtml += `<th class="text-end text-nowrap">${r.name}</th>`;
                });
                headHtml += '<th class="text-end fw-bold">Total</th></tr>';
                thead.innerHTML = headHtml;

                // 2. Build Tbody (Baris Data)
                if (data.rekap.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="${data.rombels.length + 3}" class="text-center py-4">Belum ada data transaksi setoran.</td></tr>`;
                    return;
                }

                let bodyHtml = '';
                data.rekap.forEach((row, index) => {
                    bodyHtml += `<tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-nowrap">${formatTanggal(row.tanggal)}</td>`;

                    data.rombels.forEach(r => {
                        let nominal = row['rombel_' + r.id] || 0;
                        bodyHtml += `<td class="text-end">${nominal === 0 ? '0' : formatRupiah(nominal)}</td>`;
                    });

                    bodyHtml += `<td class="text-end fw-bold text-success">${formatRupiah(row.total_semua)}</td></tr>`;
                });

                tbody.innerHTML = bodyHtml;
            })
            .catch(error => {
                tbody.innerHTML = '<tr><td colspan="100%" class="text-center text-danger py-4">Terjadi kesalahan saat memuat data.</td></tr>';
                console.error('Error fetching data:', error);
            });
    }

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function formatTanggal(tgl) {
        let date = new Date(tgl);
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }
</script>

<script>
    // Logic untuk merubah tampilan tabel berdasarkan Select Option
    function showTable(rombelId) {
        const tables = document.querySelectorAll('.rombel-table-container');
        tables.forEach(table => {
            table.style.display = 'none';
        });

        const selectedTable = document.getElementById('table' + rombelId);
        if (selectedTable) {
            selectedTable.style.display = 'block';
        }
    }

    // Jalankan saat halaman pertama kali di-load
    document.addEventListener("DOMContentLoaded", () => {
        const selectElement = document.getElementById('selectRombel');
        if (selectElement && selectElement.value) {
            showTable(selectElement.value);
        }
    });

    // Fungsi Batal Transaksi (Butuh jQuery)
    function batalTransaksi(id, nama, nominal, tipe) {
        let formatNominal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(nominal);
        
        $('#batalNama').text(nama);
        $('#batalNominal').text(formatNominal);
        $('#batalTipe').text(tipe);
        
        let url = "{{ url('/bendahara/tabungan/batal') }}/" + id;
        $('#formBatal').attr('action', url);
        
        $('input[name="konfirmasi"]').val('');
        
        $('#batalModal').modal('show');
    }
</script>
@endsection