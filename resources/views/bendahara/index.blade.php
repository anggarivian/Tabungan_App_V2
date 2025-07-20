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
            <div class="row">
                <div class="col-12 col-lg-4 col-md-12">
                    <div class="card shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="iconly-boldProfile"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Jumlah Penabung</h6>
                                    <h6 class="font-extrabold mb-0">{{$jumlah_penabung}} Orang</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-6">
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
                </div>
                <!-- Row kedua -->
                <div class="col-6 col-lg-6 col-md-6">
                    <div class="card shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
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
                <div class="col-6 col-lg-6 col-md-6">
                    <div class="card shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
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
                <div class="col-12 col-lg-12 col-md-12">
                    <div class="card shadow-lg">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon purple mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-230 -250 1000 1000"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M0 112.5L0 422.3c0 18 10.1 35 27 41.3c87 32.5 174 10.3 261-11.9c79.8-20.3 159.6-40.7 239.3-18.9c23 6.3 48.7-9.5 48.7-33.4l0-309.9c0-18-10.1-35-27-41.3C462 15.9 375 38.1 288 60.3C208.2 80.6 128.4 100.9 48.7 79.1C25.6 72.8 0 88.6 0 112.5zM288 352c-44.2 0-80-43-80-96s35.8-96 80-96s80 43 80 96s-35.8 96-80 96zM64 352c35.3 0 64 28.7 64 64l-64 0 0-64zm64-208c0 35.3-28.7 64-64 64l0-64 64 0zM512 304l0 64-64 0c0-35.3 28.7-64 64-64zM448 96l64 0 0 64c-35.3 0-64-28.7-64-64z"/></svg>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Hasil Penarikan | Perkiraan Hasil</h6>
                                    <h6 class="font-extrabold mb-0">Rp. {{ number_format($bendahara->saldo)}} | ~Rp. {{ number_format($premi)}}</h6>
                                </div>
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
                                Frekuensi Menabung ({{ count($chart_frekuensi) }}x)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="total-tab" data-bs-toggle="tab" data-bs-target="#total-tab-pane" type="button" role="tab" aria-controls="total-tab-pane" aria-selected="false">
                                Pertumbuhan Tabungan
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

    // Grafik untuk frekuensi
    var optionsFrekuensi = {
        series: [{
            name: 'Data Frekuensi',
            data: chartDataFrekuensi
        }],
        chart: {
            type: 'area',
            stacked: false,
            height: 350,
            zoom: {
                type: 'x',
                enabled: true,
                autoScaleYaxis: true
            },
            toolbar: {
                autoSelected: 'zoom'
            }
        },
        dataLabels: {
            enabled: false
        },
        markers: {
            size: 0
        },
        title: {
            text: 'Jumlah Menabung',
            align: 'left'
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.5,
                opacityTo: 0,
                stops: [0, 90, 100]
            }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "Rp. " + (val).toLocaleString('id-ID');
                }
            },
            title: {
                text: 'Rupiah (Rp.)'
            }
        },
        xaxis: {
            type: 'datetime',
            labels: {
                format: 'dd MMM yyyy'
            }
        },
        tooltip: {
            shared: false,
            y: {
                formatter: function (val) {
                    return "Rp. " + (val).toLocaleString('id-ID');
                }
            }
        }
    };

    var chartFrekuensi = new ApexCharts(document.querySelector("#frekuensi"), optionsFrekuensi);
    chartFrekuensi.render();

    var optionsTotal = {
        series: [{
            name: 'Data Total',
            data: chartDataTotal
        }],
        chart: {
            type: 'area',
            stacked: false,
            height: 350,
            zoom: {
                type: 'x',
                enabled: true,
                autoScaleYaxis: true
            },
            toolbar: {
                autoSelected: 'zoom'
            }
        },
        dataLabels: {
            enabled: false
        },
        markers: {
            size: 0
        },
        title: {
            text: 'Jumlah Saldo',
            align: 'left'
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.5,
                opacityTo: 0,
                stops: [0, 90, 100]
            }
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return "Rp. " + (val).toLocaleString('id-ID');
                }
            },
            title: {
                text: 'Rupiah (Rp.)'
            }
        },
        xaxis: {
            type: 'datetime',
            labels: {
                format: 'dd MMM yyyy'
            }
        },
        tooltip: {
            shared: false,
            y: {
                formatter: function (val) {
                    return "Rp. " + (val).toLocaleString('id-ID');
                }
            }
        }
    };

    var chartTotal = new ApexCharts(document.querySelector("#total"), optionsTotal);
    chartTotal.render();
</script>
@endsection
