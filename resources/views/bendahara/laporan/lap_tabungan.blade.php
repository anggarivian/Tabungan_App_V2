@extends('layout.main')

@section('title') Laporan Tabungan - SakuRame @endsection

@section('content')
<div class="page-heading mb-2">
    <div class="d-flex justify-content-between ">
        <h3 class="mt-3">Laporan Tabungan</h3>
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
                <li class="breadcrumb-item active" aria-current="page">Laporan Tabungan</li>
            </ol>
        </nav>
    </div>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-body" style="margin-bottom: -20px">
            <form action="/bendahara/laporan/tabungan" method="GET">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                            <p class="card-title" style="margin-top: 7px">Filter :</p>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 mb-2 mb-lg-0">
                            <input type="text" class="form-control" style="padding-right: 1px" name="search" id="search" value="{{ request('search') }}" placeholder="Cari Nama / ID Tabungan...">
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
                            <select class="form-select" name="sort_saldo" id="sort_saldo">
                                <option value="">Urutkan Saldo</option>
                                <option value="desc" {{ request('sort_saldo') == 'desc' ? 'selected' : '' }}>Banyak ke Kecil</option>
                                <option value="asc" {{ request('sort_saldo') == 'asc' ? 'selected' : '' }}>Kecil ke Banyak</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                            <button type="submit" class="btn btn-primary w-100">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
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
                            <th class="text-center">Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user as $users)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $users->username }}</td>
                                <td>{{ $users->name }}</td>
                                <td class="text-center">{{ $users->kelas->name ?? '-' }}</td>
                                <td class="text-center">Rp. {{ $users->tabungan->saldo ?? '-' }}</td>
                                <td class="text-center">Rp. {{ $users->tabungan->sisa ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Data Kosong</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
                {{ $user->links('layout.pagination.bootstrap-5') }}
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')

@endsection
