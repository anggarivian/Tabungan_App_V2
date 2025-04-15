@extends('layout.main')

@section('title') Laporan Transaksi - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Laporan Transaksi</h3>
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
                    <li class="breadcrumb-item active" aria-current="page">Laporan Transaksi</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="page-content">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tabungan-tab" data-bs-toggle="tab" data-bs-target="#tabungan" type="button" role="tab" aria-controls="tabungan" aria-selected="true">Siswa</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi" type="button" role="tab" aria-controls="transaksi" aria-selected="false">Kelas</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tabungan" role="tabpanel" aria-labelledby="tabungan-tab">
                        <div class="mt-3">
                            <form action="/bendahara/laporan/transaksi" method="GET">
                                <div class="container">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                                            <p class="card-title" style="margin-bottom: 20px">Filter :</p>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                                            <input type="text" class="form-control" style="margin-bottom: 20px" name="search" id="search" value="{{ request('search') }}" placeholder="Cari Nama / ID Tabungan...">
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                                            <select class="form-select" style="margin-bottom: 20px" name="kelas" id="kelas">
                                                <option value="">Kelas</option>
                                                @foreach ($kelasList as $kelas)
                                                    <option value="{{ $kelas->id }}"
                                                        {{ request('kelas') == $kelas->id ? 'selected' : '' }}>
                                                        {{ $kelas->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                                            <select class="form-select" style="margin-bottom: 20px" name="tipe_transaksi" id="tipe_transaksi">
                                                <option value="">Transaksi</option>
                                                <option value="stor" {{ request('tipe_transaksi') == 'stor' ? 'selected' : '' }}>Stor</option>
                                                <option value="tarik" {{ request('tipe_transaksi') == 'tarik' ? 'selected' : '' }}>Tarik</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                                            <select class="form-select" style="margin-bottom: 20px" name="sort_tanggal" id="sort_tanggal">
                                                <option value="">Urutkan Tanggal</option>
                                                <option value="desc" {{ request('sort_tanggal') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                                                <option value="asc" {{ request('sort_tanggal') == 'asc' ? 'selected' : '' }}>Terlama</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                                            <select class="form-select" style="margin-bottom: 20px" name="tipe_pembayaran" id="tipe_pembayaran">
                                                <option value="">Pembayaran</option>
                                                <option value="online" {{ request('tipe_pembayaran') == 'online' ? 'selected' : '' }}>Online</option>
                                                <option value="offline" {{ request('tipe_pembayaran') == 'offline' ? 'selected' : '' }}>Offline</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-6 mb-2 mb-lg-0">
                                            <p class="card-title text-end" style="margin-top: 7px">Dari :</p>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}" placeholder="Tanggal Mulai">
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-1 mb-2 mb-lg-0">
                                            <p class="card-title text-end" style="margin-top: 7px">s/d :</p>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}" placeholder="Tanggal Akhir">
                                        </div>
                                        <div class="col-12 col-md-12 col-lg-1 mb-2 mb-lg-0">
                                            <button type="submit" class="btn btn-primary w-100">
                                                Cari
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-hover" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th colspan="1" class="text-center">No.</th>
                                            <th class="text-center">ID</th>
                                            <th>Nama</th>
                                            <th class="text-center">Kelas</th>
                                            <th class="text-center">Saldo Awal</th>
                                            <th class="text-center">Jumlah Transaksi</th>
                                            <th class="text-center">Saldo Akhir</th>
                                            <th class="text-center">Transaksi</th>
                                            <th class="text-center">Pembuat</th>
                                            <th class="text-center">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transaksi as $transaksis)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="text-center">{{ $transaksis->user->username }}</td>
                                                <td>{{ Str::limit($transaksis->user->name, 20, '...') }}</td>
                                                <td class="text-center">{{ $transaksis->user->kelas->name ?? '-' }}</td>
                                                <td class="text-center">Rp. {{ number_format($transaksis->saldo_awal ?? 0) }}</td>
                                                <td class="text-center">Rp. {{ number_format($transaksis->jumlah_transaksi ?? 0) }}</td>
                                                <td class="text-center">Rp. {{ number_format($transaksis->saldo_akhir ?? 0) }}</td>
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
                                                <td class="text-center">{{ Str::limit($transaksis->pembuat, 20, '...') }}</td>
                                                <td class="text-center">{{ \Carbon\Carbon::parse($transaksis->created_at)->format('d M Y H:i') }}</td>
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
                                        <div class="d-flex justify-content-end mb-3">
                                            <label for="perPage" style="margin-top: 3px">Show</label>
                                            <select name="perPage" id="perPage" class="form-select form-control-sm form-select-sm mx-2" onchange="this.form.submit()">
                                                <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                                <option value="75" {{ request('perPage') == 75 ? 'selected' : '' }}>75</option>
                                                <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                                                <option value="1000" {{ request('perPage') == 1000 ? 'selected' : '' }}>Semua</option>
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
                    <div class="tab-pane fade" id="transaksi" role="tabpanel" aria-labelledby="transaksi-tab">
                        <div class="mt-3">
                            <div class="table-responsive">
                                <table class="table table-hover" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th colspan="1" class="text-center">No.</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Kelas 1</th>
                                            <th class="text-center">Kelas 2</th>
                                            <th class="text-center">Kelas 3</th>
                                            <th class="text-center">Kelas 4</th>
                                            <th class="text-center">Kelas 5</th>
                                            <th class="text-center">Kelas 6</th>
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transaksi as $transaksis)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="text-center">{{ \Carbon\Carbon::parse($transaksis->created_at)->format('d M Y') }}</td>
                                                <td class="text-center">Rp. {{ number_format($transaksis->saldo_akhir ?? 0) }}</td>
                                                <td class="text-center">Rp. {{ number_format($transaksis->saldo_akhir ?? 0) }}</td>
                                                <td class="text-center">Rp. {{ number_format($transaksis->saldo_akhir ?? 0) }}</td>
                                                <td class="text-center">Rp. {{ number_format($transaksis->saldo_akhir ?? 0) }}</td>
                                                <td class="text-center">Rp. {{ number_format($transaksis->saldo_akhir ?? 0) }}</td>
                                                <td class="text-center">Rp. {{ number_format($transaksis->saldo_akhir ?? 0) }}</td>
                                                <td class="text-center">Rp. {{ number_format($transaksis->saldo_akhir ?? 0) }}</td>
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
                                        <div class="d-flex justify-content-end mb-3">
                                            <label for="perPage" style="margin-top: 3px">Show</label>
                                            <select name="perPage" id="perPage" class="form-select form-control-sm form-select-sm mx-2" onchange="this.form.submit()">
                                                <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                                <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                                <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                                <option value="75" {{ request('perPage') == 75 ? 'selected' : '' }}>75</option>
                                                <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                                                <option value="1000" {{ request('perPage') == 1000 ? 'selected' : '' }}>Semua</option>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection
