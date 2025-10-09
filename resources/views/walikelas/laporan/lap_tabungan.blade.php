@extends('layout.main')

@section('title') Laporan Tabungan - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Laporan Tabungan Kelas {{$kelas}}</h3>
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
                    <li class="breadcrumb-item active" aria-current="page">Laporan Tabungan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="card shadow">
        <div class="card-body" style="margin-bottom: -20px">
            <form action="/walikelas/laporan/tabungan" method="GET">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 col-lg-4 mb-2 mb-lg-0">
                            <p class="card-title" style="margin-top: 7px">Filter :</p>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4 mb-2 mb-lg-0">
                            <input type="text" class="form-control" style="padding-right: 1px" name="search" id="search" value="{{ request('search') }}" placeholder="Cari Nama / ID Tabungan...">
                        </div>
                        <div class="col-12 col-md-6 col-lg-2 mb-2 mb-lg-0">
                            <select class="form-select" name="sort_saldo" id="sort_saldo">
                                <option value="">Urutkan</option>
                                <option value="desc" {{ request('sort_saldo') == 'desc' ? 'selected' : '' }}>Banyak ke Kecil</option>
                                <option value="asc" {{ request('sort_saldo') == 'asc' ? 'selected' : '' }}>Kecil ke Banyak</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-12 col-lg-2 mb-2 mb-lg-0">
                            <button type="submit" class="btn btn-primary w-100">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
        <div class="card-body pb-1 pt-3">
            <div class="table-responsive">
                <table class="table table-hover" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 100px;">No.</th>
                            <th class="text-center">ID</th>
                            <th>Nama</th>
                            <th class="text-center">Saldo</th>
                            <th class="text-center">Sisa</th>
                            {{-- <th class="text-center">Tanggal</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user as $users)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $users->username }}</td>
                                <td>{{ $users->name }}</td>
                                <td class="text-center">Rp. {{ number_format($users->tabungan->saldo ?? 0 ) }}</td>
                                <td class="text-center">Rp. {{ number_format($users->tabungan->sisa ?? 0 ) }}</td>
                                {{-- <td class="text-center">Rp. {{ $users->created_at }}</td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Data Kosong</td>
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
                        {{ $user->appends(['perPage' => request('perPage')])->links('layout.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')

@endsection
