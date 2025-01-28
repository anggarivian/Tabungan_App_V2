@extends('layout.main')

@section('title') Tabungan - SakuRame @endsection

@section('content')
<style>
    .table-container {
        display: none;
        margin-top: 10px;
    }
    .trigger {
        background-color: #007bff;
        color: white;
        padding: 10px;
        cursor: pointer;
        text-align: center;
        margin-bottom: 5px;
        width: 200px;
    }
</style>
<div class="page-heading mb-2">
    <div class="d-flex justify-content-between">
        <h3 class="mt-3">Informasi Tabungan Hari Ini</h3>
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
                <li class="breadcrumb-item active" aria-current="page">Tabungan</li>
            </ol>
        </nav>
    </div>
</div>
<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-12">
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class=" col-6 col-md-2 col-sm-6 mb-3 mb-sm-0 mb-md-0 mb-lg-0  d-flex justify-content-center ">
                                    <div class="stats-icon green mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-230 -230 1000 1000"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M352 96l64 0c17.7 0 32 14.3 32 32l0 256c0 17.7-14.3 32-32 32l-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l64 0c53 0 96-43 96-96l0-256c0-53-43-96-96-96l-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32zm-9.4 182.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L242.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/></svg>
                                    </div>
                                </div>
                                <div class=" col-6 col-md-4 col-sm-6">
                                    <h6 class="text-muted font-semibold">Uang Masuk</h6>
                                    <h6 class="font-extrabold mb-0">Rp. {{ number_format($transaksi_masuk)}}</h6>
                                </div>
                                <div class=" col-6 col-md-2 col-sm-6 d-flex justify-content-center">
                                    <div class="stats-icon red mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-230 -230 1000 1000"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128zM160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0z"/></svg>
                                    </div>
                                </div>
                                <div class=" col-6 col-md-4 col-sm-6">
                                    <h6 class="text-muted font-semibold">Uang Keluar</h6>
                                    <h6 class="font-extrabold mb-0">Rp. {{ number_format($transaksi_keluar)}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-12 col-sm-12 ">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-6 col-md-3 col-sm-6 d-flex justify-content-center ">
                                    <div class="stats-icon purple mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-230 -250 1000 1000"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M0 112.5L0 422.3c0 18 10.1 35 27 41.3c87 32.5 174 10.3 261-11.9c79.8-20.3 159.6-40.7 239.3-18.9c23 6.3 48.7-9.5 48.7-33.4l0-309.9c0-18-10.1-35-27-41.3C462 15.9 375 38.1 288 60.3C208.2 80.6 128.4 100.9 48.7 79.1C25.6 72.8 0 88.6 0 112.5zM288 352c-44.2 0-80-43-80-96s35.8-96 80-96s80 43 80 96s-35.8 96-80 96zM64 352c35.3 0 64 28.7 64 64l-64 0 0-64zm64-208c0 35.3-28.7 64-64 64l0-64 64 0zM512 304l0 64-64 0c0-35.3 28.7-64 64-64zM448 96l64 0 0 64c-35.3 0-64-28.7-64-64z"/></svg>
                                    </div>
                                </div>
                                <div class="col-6 col-md-9 col-sm-6">
                                    <h6 class="text-muted font-semibold">Jumlah Tunai</h6>
                                    <h6 class="font-extrabold mb-0">Rp. {{ number_format($jumlah_saldo)}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-12 col-sm-12 ">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-6 col-md-3 col-sm-6 d-flex justify-content-center ">
                                    <div class="stats-icon blue mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-230 -250 1000 1000"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M64 32C28.7 32 0 60.7 0 96l0 32 576 0 0-32c0-35.3-28.7-64-64-64L64 32zM576 224L0 224 0 416c0 35.3 28.7 64 64 64l448 0c35.3 0 64-28.7 64-64l0-192zM112 352l64 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-64 0c-8.8 0-16-7.2-16-16s7.2-16 16-16zm112 16c0-8.8 7.2-16 16-16l128 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-128 0c-8.8 0-16-7.2-16-16z"/></svg>
                                    </div>
                                </div>
                                <div class="col-6 col-md-9 col-sm-6">
                                    <h6 class="text-muted font-semibold">Jumlah Digital</h6>
                                    <h6 class="font-extrabold mb-0">Rp. 0</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="row justify-content-center">
        <div class="container">
            <div class="card col-12">
                <div class="card-body">
                    <h5 class="card-title">Pilih Transaksi</h5>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('walikelas.tabungan.stor') }}" class="btn btn-primary w-100 m-1 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 50 448 512">
                                <path fill="#ffffff" d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
                            </svg>
                            Stor Per Orang
                        </a>
                        <a href="{{ route('walikelas.tabungan.stor.masal') }}" class="btn btn-primary w-100 m-1 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 50 448 512">
                                <path fill="#ffffff" d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/>
                            </svg>
                            Stor Per Kelas
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Transaksi Hari Ini</h5>
                    <div class="dropdown-table mt-3 w-100">
                        <div class="table-responsive-lg">
                            <table class="table table-hover" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th class="text-center">ID</th>
                                        <th>Nama</th>
                                        <th class="text-center">Kelas</th>
                                        <th class="text-center">Saldo Awal</th>
                                        <th class="text-center">Jumlah Transaksi</th>
                                        <th class="text-center">Saldo Akhir</th>
                                        <th class="text-center">Transaksi</th>
                                        <th class="text-center">Pembuat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($kelas as $transaksis)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $transaksis->user->username }}</td>
                                            <td>{{ $transaksis->user->name }}</td>
                                            <td class="text-center">{{ $transaksis->user->kelas->name ?? '-' }}</td>
                                            <td class="text-center">Rp. {{ number_format($transaksis->saldo_awal ?? 0 ) }}</td>
                                            <td class="text-center">Rp. {{ number_format($transaksis->jumlah_transaksi ?? 0 ) }}</td>
                                            <td class="text-center">Rp. {{ number_format($transaksis->saldo_akhir ?? 0 ) }}</td>
                                            <td class="text-center">
                                                @if ($transaksis->tipe_transaksi == 'Stor')
                                                    <span class="badge bg-success">Stor</span>
                                                    <span class="badge bg-light">{{ $transaksis->pembayaran ?? '-' }}</span>
                                                @elseif ($transaksis->tipe_transaksi == 'Tarik')
                                                    <span class="badge bg-danger">Tarik</span>
                                                    <span class="badge bg-light">{{ $transaksis->pembayaran ?? '-' }}</span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                    <span class="badge bg-light">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $transaksis->pembuat ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">Data Kosong</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between">
                                <form method="GET" action="{{ request()->url() }}">
                                    <div class="d-flex justify-content-end mb-0">
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
                                    {{ $kelas->appends(['perPage' => request('perPage')])->links('layout.pagination.bootstrap-5') }}
                                </div>
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
