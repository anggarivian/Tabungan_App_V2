@extends('layout.main')

@section('title') Dashboard - SakuRame @endsection

@section('content')
<style>
    .page-heading h3 {
        font-size: 1.8rem;
        background: linear-gradient(90deg, #667eea, #764ba2, #667eea, #764ba2, #667eea);
        background-size: 200% 200%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: darkWave 3s ease-in-out infinite, slideInUp 2s ease-out;
        font-weight: 800;
    }

    .page-heading h4 {
        font-size: 1.2rem;
        background: linear-gradient(90deg, #667eea, #764ba2, #667eea, #764ba2, #667eea);
        background-size: 200% 200%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: darkWave 3s ease-in-out infinite, slideInUp 2s ease-out;
        font-weight: 200;
    }

    @keyframes darkWave {
        0%, 100% { background-position: 0% 50% }
        50% { background-position: 100% 50% }
    }
</style>
<div class="page-heading">
    <h4>Selamat Datang, </h4>
    <h3>{{auth()->user()->name}}</h3>
</div>
<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-12">
            <div class="card shadow-lg portrait-card d-none">
                <div class="card-body py-3">
                    <p class="mb-0 d-flex align-items-center justify-content-between">
                        <span class="fw-medium">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Pilih Menu di kanan atas untuk Stor/Tarik, dan Laporan Tabungan
                        </span>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-4 col-md-6">
                    <div class="card shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <i class="iconly-boldWallet"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Saldo Tunai</h6>
                                    <h6 class="font-extrabold mb-0">Rp. {{ number_format($jumlah_saldo_tunai)}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-6">
                    <div class="card shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class=" col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-230 -230 1000 1000"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M352 96l64 0c17.7 0 32 14.3 32 32l0 256c0 17.7-14.3 32-32 32l-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l64 0c53 0 96-43 96-96l0-256c0-53-43-96-96-96l-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32zm-9.4 182.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L242.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/></svg>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Transaksi Masuk</h6>
                                    <h6 class="font-extrabold mb-0">Rp. {{ number_format($transaksi_masuk)}} | {{$storKali}}x</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-6">
                    <div class="card shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class=" col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon red mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-230 -230 1000 1000"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128zM160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0z"/></svg>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Transaksi Keluar</h6>
                                    <h6 class="font-extrabold mb-0">Rp. {{ number_format($transaksi_keluar)}} | {{$tarikKali}}x</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-12 col-lg-3 col-md-6">
                    <div class="card shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon green mb-2">
                                        <i class="iconly-boldWallet"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Saldo Digital</h6>
                                    <h6 class="font-extrabold mb-0">Rp. {{ number_format($jumlah_saldo_digital)}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
            <h3 class="mb-3">Tabungan</h3>
            <div class="card shadow-lg">
                <div class="card-body">
                    <h5 class="card-title">Buku Tabungan</h5>
                    <p class="text-muted fst-italic">Menampilkan transaksi terbaru di atas</p>
                    <div class="table-responsive">
                        <table class="table table-hover" style="width: 100%">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center">
                                        Tanggal <i class="bi bi-arrow-down-short"></i>
                                    </th>
                                    <th colspan="2" class="text-center">Tabungan</th>
                                    <th rowspan="2" class="text-center">Jumlah</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Masuk</th>
                                    <th class="text-center">Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksi as $transaksis)
                                    <tr>
                                        <td class="text-center"> <span class="badge bg-light">{{ $transaksis->created_at->format('d-m-y') }}</span></td>
                                        <td class="text-center">
                                            @if ($transaksis->tipe_transaksi == 'Stor')
                                                <span class="badge bg-success">+  {{ number_format($transaksis->jumlah_transaksi ?? 0) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($transaksis->tipe_transaksi == 'Tarik')
                                                <span class="badge bg-danger">-  {{ number_format($transaksis->jumlah_transaksi ?? 0) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"> {{ number_format($transaksis->saldo_akhir ?? 0) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Data Kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between">
                            <form method="GET" action="{{ request()->url() }}">
                                <div class="d-flex justify-content-end mb-3">
                                    <label for="perPage" style="margin-top: 3px">Show</label>
                                    <select name="perPage" id="perPage" class="form-select form-control-sm form-select-sm mx-2" onchange="this.form.submit()">
                                        <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="75" {{ request('perPage') == 75 ? 'selected' : '' }}>75</option>
                                        <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                            </form>
                            <div class="justify-content-end">
                                {{ $transaksi->appends(['perPage' => request('perPage')])->links('layout.pagination.bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-lg">
                <div class="card-body">
                    <h5 class="card-title">Statistik Tabungan</h5>

                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="tabunganTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="frekuensi-tab" data-bs-toggle="tab" data-bs-target="#frekuensi-tab-pane" type="button" role="tab" aria-controls="frekuensi-tab-pane" aria-selected="true">
                                Frekuensi ({{ count($chart_frekuensi) }}x)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="total-tab" data-bs-toggle="tab" data-bs-target="#total-tab-pane" type="button" role="tab" aria-controls="total-tab-pane" aria-selected="false">
                                Pertumbuhan
                            </button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content pt-3" id="tabunganTabsContent">
                        <div class="tab-pane fade show active" id="frekuensi-tab-pane" role="tabpanel" aria-labelledby="frekuensi-tab" tabindex="0">
                            <div id="frekuensi"></div>
                        </div>
                        <div class="tab-pane fade" id="total-tab-pane" role="tabpanel" aria-labelledby="total-tab" tabindex="0">
                            <div id="total"></div>
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

    const commonOptions = {
        chart: {
            type: 'area',
            height: 350,
            stacked: false,
            foreColor: '#373d3f',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true,
                },
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 700,
                animateGradually: {
                    enabled: true,
                    delay: 200
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            },
            zoom: {
                enabled: true,
                type: 'x',
                autoScaleYaxis: true
            },
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        dataLabels: {
            enabled: false
        },
        markers: {
            size: 6,
            hover: {
                sizeOffset: 3
            }
        },
        grid: {
            borderColor: '#e0e0e0',
            row: {
                colors: ['transparent'],
                opacity: 0
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: "vertical",
                shadeIntensity: 0.5,
                gradientToColors: undefined,
                inverseColors: true,
                opacityFrom: 0.7,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            },
        },
        xaxis: {
            type: 'datetime',
            labels: {
                format: 'dd MMM',
                style: {
                    fontSize: '13px'
                }
            },
            tooltip: {
                enabled: false
            }
        },
        yaxis: {
            labels: {
                formatter: val => "Rp. " + val.toLocaleString('id-ID'),
                style: {
                    fontSize: '13px'
                }
            },
            title: {
                text: 'Jumlah (Rp)'
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            theme: 'light',
            x: {
                format: 'dd MMM yyyy'
            },
            y: {
                formatter: val => "Rp. " + val.toLocaleString('id-ID')
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            floating: false,
            offsetY: -10,
            offsetX: -5
        }
    };

    const optionsFrekuensi = {
        ...commonOptions,
        series: [{
            name: 'Frekuensi Menabung',
            data: chartDataFrekuensi
        }],
        colors: ['#00b894'],
        title: {
            text: 'Frekuensi Menabung Siswa',
            align: 'left',
            style: {
                fontSize: '16px',
                fontWeight: 'bold',
                color: '#2d3436'
            }
        }
    };

    const optionsTotal = {
        ...commonOptions,
        series: [{
            name: 'Total Tabungan',
            data: chartDataTotal
        }],
        colors: ['#0984e3'],
        title: {
            text: 'Pertumbuhan Total Tabungan',
            align: 'left',
            style: {
                fontSize: '16px',
                fontWeight: 'bold',
                color: '#2d3436'
            }
        }
    };

    new ApexCharts(document.querySelector("#frekuensi"), optionsFrekuensi).render();
    new ApexCharts(document.querySelector("#total"), optionsTotal).render();
</script>

@endsection
