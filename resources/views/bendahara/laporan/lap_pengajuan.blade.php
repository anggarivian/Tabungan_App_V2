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
            <form action="/bendahara/laporan/pengajuan" method="GET">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 col-lg-1 mb-2 mb-lg-0">
                            <p class="card-title" style="margin-top: 7px">Filter :</p>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 mb-2 mb-lg-0">
                            <input type="text" class="form-control" style="padding-right: 1px" name="search" id="search" value="{{ request('search') }}" placeholder="Cari Nama atau Username...">
                        </div>
                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                            <select class="form-select" name="kelas" id="kelas">
                                <option value="">Kelas</option>
                                <option value="1A" {{ request('kelas') == '1A' ? 'selected' : '' }}>1 - A</option>
                                <option value="1B" {{ request('kelas') == '1B' ? 'selected' : '' }}>1 - B</option>
                                <option value="2A" {{ request('kelas') == '2A' ? 'selected' : '' }}>2 - A</option>
                                <option value="2B" {{ request('kelas') == '2B' ? 'selected' : '' }}>2 - B</option>
                                <option value="3A" {{ request('kelas') == '3A' ? 'selected' : '' }}>3 - A</option>
                                <option value="3B" {{ request('kelas') == '3B' ? 'selected' : '' }}>3 - B</option>
                                <option value="4" {{ request('kelas') == '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ request('kelas') == '5' ? 'selected' : '' }}>5</option>
                                <option value="6" {{ request('kelas') == '6' ? 'selected' : '' }}>6</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                            <select class="form-select" name="status" id="status">
                                <option value="">Status</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Diterima</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 mb-2 mb-lg-0">
                            <select class="form-select" name="sort_penarikan" id="sort_penarikan">
                                <option value="">Urutkan Penarikan</option>
                                <option value="desc" {{ request('sort_penarikan') == 'desc' ? 'selected' : '' }}>Banyak ke Kecil</option>
                                <option value="asc" {{ request('sort_penarikan') == 'asc' ? 'selected' : '' }}>Kecil ke Banyak</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-1 mb-2 mb-lg-0">
                            <button type="submit" class="btn btn-primary w-100">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        {{-- Table Tabungan --}}
        <div class="card-body pb-1 pt-3">
            <div class="table-responsive-lg">
                <table class="table table-hover" style="width: 100%">
                    <thead>
                        <tr>
                            <th colspan="1" class="text-center">No.</th>
                            <th class="text-center">ID</th>
                            <th>Nama</th>
                            <th class="text-center">Kelas</th>
                            <th class="text-center">Saldo</th>
                            <th class="text-center">Jumlah Penarikan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Pembayaran</th>
                            <th class="text-center">Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan as $pengajuans)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $pengajuans->user->username }}</td>
                                <td>{{ $pengajuans->user->name }}</td>
                                <td class="text-center">{{ $pengajuans->user->kelas->name ?? '-' }}</td>
                                <td class="text-center">Rp. {{ $pengajuans->tabungan->saldo ?? '-' }}</td>
                                <td class="text-center">{{ $pengajuans->jumlah_penarikan ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($pengajuans->status == 'Pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif ($pengajuans->status == 'Tolak')
                                        <span class="badge bg-danger">Tolak</span>
                                    @elseif ($pengajuans->status == 'Terima')
                                        <span class="badge bg-success">Terima</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $pengajuans->pembayaran ?? '-' }}</td>
                                <td class="text-center">{{ $pengajuans->alasan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Data Kosong</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
                {{ $pengajuan->links('layout.pagination.bootstrap-5') }}
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')

@endsection
