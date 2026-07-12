@extends('layout.main')

@section('title') Dashboard - SakuRame @endsection

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
    <h4>Selamat Datang kembali,</h4>
    <h3>{{auth()->user()->name}}</h3>
</div>

<div class="page-content">
    <div class="row g-3 mb-3 mt-4">
        <div class="col-12 col-md-6 col-xl-6 mt-3">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-blue-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Jumlah Penabung</h6>
                        <h5 class="font-extrabold mb-0">{{$jumlah_penabung}} <span class="fs-6 text-muted fw-normal">Orang</span></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-6 mt-3">
            <div class="card stat-card shadow-sm h-100 mb-0">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-green-light me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16"><path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/><path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/></svg>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 font-semibold">Saldo Tunai</h6>
                        <h5 class="font-extrabold mb-0">Rp {{ number_format($jumlah_saldo_tunai) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-6 mt-3">
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

        <div class="col-12 col-md-6 col-xl-6 mt-3">
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

    <div class="row">
        <div class="col-12">
            <div class="card stat-card shadow-sm border-0 rounded-4">
                <div class="card-header border-0 pt-4 pb-0 px-4">
                    <h5 class="card-title fw-bold mb-3">Statistik Tabungan</h5>
                    
                    <ul class="nav custom-tabs" id="tabunganTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="frekuensi-tab" data-bs-toggle="tab" data-bs-target="#frekuensi-tab-pane" type="button" role="tab" aria-controls="frekuensi-tab-pane" aria-selected="true">
                                Frekuensi Menabung ({{ count($chart_frekuensi) }}x)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="total-tab" data-bs-toggle="tab" data-bs-target="#total-tab-pane" type="button" role="tab" aria-controls="total-tab-pane" aria-selected="false">
                                Pertumbuhan Tabungan
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body px-4 pb-4">
                    <div class="tab-content mt-2" id="tabunganTabsContent">
                        <div class="tab-pane fade show active" id="frekuensi-tab-pane" role="tabpanel" aria-labelledby="frekuensi-tab" tabindex="0">
                            <div id="frekuensi" style="min-height: 350px;"></div>
                        </div>
                        <div class="tab-pane fade" id="total-tab-pane" role="tabpanel" aria-labelledby="total-tab" tabindex="0">
                            <div id="total" style="min-height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    const chartDataFrekuensi = @json($chart_frekuensi);
    const chartDataTotal = @json($chart_total);

    // Konfigurasi umum agar style seragam
    const commonChartOptions = {
        chart: {
            type: 'area',
            stacked: false,
            height: 350,
            fontFamily: 'inherit',
            toolbar: { show: false }, // Disembunyikan biar lebih clean UI-nya
            zoom: { enabled: true }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 }, // Mengubah garis jadi lebih melengkung modern
        markers: { size: 0, hover: { size: 5 } },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.4,
                opacityTo: 0,
                stops: [0, 90, 100]
            }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "Rp " + (val).toLocaleString('id-ID');
                }
            }
        },
        xaxis: {
            type: 'datetime',
            labels: { format: 'dd MMM yyyy' },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4, // Garis grid putus-putus biar minimalis
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function (val) {
                    return "Rp " + (val).toLocaleString('id-ID');
                }
            }
        }
    };

    // Render Grafik Frekuensi
    var optionsFrekuensi = {
        ...commonChartOptions,
        series: [{ name: 'Jumlah Menabung', data: chartDataFrekuensi }],
        colors: ['#0284c7'] // Warna biru modern
    };
    var chartFrekuensi = new ApexCharts(document.querySelector("#frekuensi"), optionsFrekuensi);
    chartFrekuensi.render();

    // Render Grafik Total
    var optionsTotal = {
        ...commonChartOptions,
        series: [{ name: 'Total Saldo', data: chartDataTotal }],
        colors: ['#9333ea'] // Warna ungu modern
    };
    var chartTotal = new ApexCharts(document.querySelector("#total"), optionsTotal);
    chartTotal.render();
</script>
@endsection