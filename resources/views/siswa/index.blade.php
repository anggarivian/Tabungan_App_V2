@extends('layout.main')

@section('title', 'Dashboard Siswa - SakuRame')

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
    .page-heading h4 { font-size: 1.1rem; color: #6c757d; font-weight: 400; margin-bottom: 0; }
    @keyframes gradientMove { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }

    /* Styling Custom Card & Hover Effect */
    .stat-card { border: none; border-radius: 1rem; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }

    .icon-box { width: 54px; height: 54px; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .icon-box svg { width: 28px; height: 28px; }

    /* Custom Colors for Icons */
    .bg-blue-light { background-color: #e0f2fe; color: #0284c7; }
    .bg-green-light { background-color: #dcfce7; color: #16a34a; }
    .bg-purple-light { background-color: #f3e8ff; color: #9333ea; }
    .bg-red-light { background-color: #fee2e2; color: #dc2626; }
    
    /* Custom Tabs */
    .custom-tabs .nav-link { color: #6c757d; font-weight: 600; border: none; border-bottom: 3px solid transparent; padding: 0.75rem 1.5rem; }
    .custom-tabs .nav-link.active { color: #764ba2; border-bottom-color: #764ba2; background: transparent; }
    
    /* Perbaikan minor header tabel tanpa border vertikal */
    .table-clean th { border-bottom: 2px solid #ebebeb; color: #6c757d; font-weight: 600; }
</style>

<div class="page-heading mb-4">
    <h4>Selamat Datang,</h4>
    <h3>{{ auth()->user()->name }}</h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-12">
            
            {{-- Info Banner Mobile --}}
            <div class="card shadow-sm border-0 portrait-card d-none mb-3">
                <div class="card-body py-3">
                    <p class="mb-0 d-flex align-items-center justify-content-between text-info">
                        <span class="fw-medium">
                            <i class="bi bi-info-circle-fill me-2"></i> Pilih Menu di kanan atas untuk Stor/Tarik, dan Laporan Tabungan
                        </span>
                    </p>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="row g-3 mb-4">
                {{-- Card Total Saldo Aktif --}}
                <div class="col-12 col-md-4 mt-3">
                    <div class="card stat-card shadow-sm h-100 mb-0">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-blue-light me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/><path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/></svg>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 font-semibold">Total Saldo Aktif</h6>
                                <h5 class="font-extrabold mb-0">Rp {{ number_format($jumlah_saldo) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Saldo Tunai --}}
                <div class="col-6 col-md-4 mt-3">
                    <div class="card stat-card shadow-sm h-100 mb-0">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-green-light me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499L12.136.326zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484L5.562 3zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-13z"/></svg>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 font-semibold">Saldo Tunai</h6>
                                <h5 class="font-extrabold mb-0">Rp {{ number_format($jumlah_saldo_tunai) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Saldo Digital --}}
                <div class="col-6 col-md-4 mt-3">
                    <div class="card stat-card shadow-sm h-100 mb-0">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-purple-light me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2Zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7Z"/><path d="M2 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 font-semibold">Saldo Digital</h6>
                                <h5 class="font-extrabold mb-0">Rp {{ number_format($jumlah_saldo_digital) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Uang Masuk --}}
                <div class="col-12 col-md-6 mt-3">
                    <div class="card stat-card shadow-sm h-100 mb-0">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-green-light me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/></svg>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 font-semibold">Total Uang Masuk</h6>
                                <h5 class="font-extrabold mb-1">Rp {{ number_format($transaksi_masuk) }}</h5>
                                <small class="text-success fw-bold"><i class="bi bi-arrow-up-circle"></i> {{ $storKali }}x Setoran</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Uang Keluar --}}
                <div class="col-12 col-md-6 mt-3">
                    <div class="card stat-card shadow-sm h-100 mb-0">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-red-light me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4z"/></svg>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 font-semibold">Total Uang Keluar</h6>
                                <h5 class="font-extrabold mb-1">Rp {{ number_format($transaksi_keluar) }}</h5>
                                <small class="text-danger fw-bold"><i class="bi bi-arrow-down-circle"></i> {{ $tarikKali }}x Penarikan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buku Tabungan (Dipindah ke atas Chart) --}}
            <div class="card stat-card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header border-0 pt-4 pb-0 px-4 bg-transparent">
                    <h5 class="card-title fw-bold mb-1">Buku Tabungan</h5>
                    <p class="text-muted fst-italic small">Menampilkan transaksi terbaru pada periode saat ini</p>
                </div>
                
                <div class="card-body px-4 pb-4">
                    <div class="table-responsive">
                        {{-- Menggunakan table biasa tanpa border kaku --}}
                        <table class="table table-hover table-clean align-middle mb-4" style="width: 100%">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center align-middle border-bottom-0 pb-0">Tanggal <i class="bi bi-arrow-down-short"></i></th>
                                    <th colspan="2" class="text-center align-middle">Tabungan</th>
                                    <th rowspan="2" class="text-center align-middle border-bottom-0 pb-0">Saldo Akhir</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Masuk</th>
                                    <th class="text-center">Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksi as $trx)
                                    <tr>
                                        <td class="text-center fw-medium">{{ $trx->created_at->format('d-m-Y') }}</td>
                                        
                                        {{-- Kolom Masuk --}}
                                        <td class="text-center">
                                            @if ($trx->tipe_transaksi == 'Stor')
                                                <span class="text-success fw-bold">+ {{ number_format($trx->jumlah_transaksi ?? 0, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Kolom Keluar --}}
                                        <td class="text-center">
                                            @if ($trx->tipe_transaksi == 'Tarik')
                                                <span class="text-danger fw-bold">- {{ number_format($trx->jumlah_transaksi ?? 0, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Kolom Saldo Akhir --}}
                                        <td class="text-center fw-bold">
                                            Rp {{ number_format($trx->saldo_akhir ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada data transaksi di periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Filter perPage & Pagination --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-2">
                        <form method="GET" action="{{ request()->url() }}" class="mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <label for="perPage" class="me-2 small text-muted">Tampilkan</label>
                                <select name="perPage" id="perPage" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                    <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10 baris</option>
                                    <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25 baris</option>
                                    <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50 baris</option>
                                    <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100 baris</option>
                                </select>
                            </div>
                        </form>
                        <div>
                            {{ $transaksi->appends(['perPage' => request('perPage')])->links('layout.pagination.bootstrap-5') }}
                        </div>
                    </div>

                </div>
            </div>

            {{-- Grafik Statistik Tabungan (Dipindah ke bawah) --}}
            <div class="card stat-card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header border-0 pt-4 pb-0 px-4 bg-transparent">
                    <h5 class="card-title fw-bold mb-3">Statistik Tabungan Saya</h5>
                    
                    <ul class="nav custom-tabs" id="tabunganTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="frekuensi-tab" data-bs-toggle="tab" data-bs-target="#frekuensi-tab-pane" type="button" role="tab">
                                Frekuensi Setoran ({{ count($chart_frekuensi) }}x)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="total-tab" data-bs-toggle="tab" data-bs-target="#total-tab-pane" type="button" role="tab">
                                Pertumbuhan Tabungan
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body px-4 pb-4">
                    <div class="tab-content mt-2" id="tabunganTabsContent">
                        <div class="tab-pane fade show active" id="frekuensi-tab-pane" role="tabpanel" tabindex="0">
                            <div id="frekuensi" style="min-height: 350px;"></div>
                        </div>
                        <div class="tab-pane fade" id="total-tab-pane" role="tabpanel" tabindex="0">
                            <div id="total" style="min-height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const chartDataFrekuensi = @json($chart_frekuensi);
    const chartDataTotal = @json($chart_total);

    // Konfigurasi Umum ApexCharts (Desain Minimalis dan Modern)
    const commonChartOptions = {
        chart: {
            type: 'area', stacked: false, height: 350, fontFamily: 'inherit',
            toolbar: { show: false }, zoom: { enabled: true }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 0, hover: { size: 5 } },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.4, opacityTo: 0, stops: [0, 90, 100] }
        },
        yaxis: { labels: { formatter: function (val) { return "Rp " + (val).toLocaleString('id-ID'); } } },
        xaxis: { type: 'datetime', labels: { format: 'dd MMM yyyy' }, axisBorder: { show: false }, axisTicks: { show: false } },
        grid: { borderColor: '#f1f1f1', strokeDashArray: 4 },
        tooltip: { theme: 'light', y: { formatter: function (val) { return "Rp " + (val).toLocaleString('id-ID'); } } }
    };

    // Render Chart Frekuensi
    var optionsFrekuensi = {
        ...commonChartOptions,
        series: [{ name: 'Nominal Setoran', data: chartDataFrekuensi }],
        colors: ['#0284c7'] // Biru Modern
    };
    new ApexCharts(document.querySelector("#frekuensi"), optionsFrekuensi).render();

    // Render Chart Total Pertumbuhan
    var optionsTotal = {
        ...commonChartOptions,
        series: [{ name: 'Total Saldo', data: chartDataTotal }],
        colors: ['#9333ea'] // Ungu Modern
    };
    new ApexCharts(document.querySelector("#total"), optionsTotal).render();
</script>
@endsection