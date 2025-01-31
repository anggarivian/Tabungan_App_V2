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
        <div class="card-body" style="margin-bottom: -20px">
            <div class="page-content">
                <div class="card">
                    <div class="card-body">
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
                                <!-- Form untuk export data tabungan -->
                                @include('bendahara.laporan.partials.tabungan')
                            </div>
                            <div class="tab-pane fade" id="transaksi" role="tabpanel" aria-labelledby="transaksi-tab">
                                <!-- Form untuk export data transaksi -->
                                @include('bendahara.laporan.partials.transaksi')
                            </div>
                            <div class="tab-pane fade" id="pengajuan" role="tabpanel" aria-labelledby="pengajuan-tab">
                                <!-- Form untuk export data pengajuan -->
                                @include('bendahara.laporan.partials.pengajuan')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection
