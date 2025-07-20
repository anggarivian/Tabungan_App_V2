@extends('layout.main')

@section('title') Laporan Pengajuan - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Laporan Pengajuan</h3>
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
                    <li class="breadcrumb-item active" aria-current="page">Laporan Pengajuan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="card shadow-lg">
        <div class="card-body" style="margin-bottom: -20px">
            <form action="/siswa/laporan/pengajuan" method="GET">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-12 col-lg-1 mb-2 mb-lg-0">
                            <p class="card-title" style="margin-bottom: 20px" >Filter :</p>
                        </div>
                        <div class="col-12 col-md-12 col-lg-2 mb-2 mb-lg-0">
                            <select class="form-select" style="margin-bottom: 20px" name="status" id="status">
                                <option value="">Status</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Terima" {{ request('status') == 'Terima' ? 'selected' : '' }}>Diterima</option>
                                <option value="Tolak" {{ request('status') == 'Tolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-12 col-lg-2 mb-2 mb-lg-0">
                            <select class="form-select" style="margin-bottom: 20px" name="sort_penarikan" id="sort_penarikan">
                                <option value="">Urutkan</option>
                                <option value="desc" {{ request('sort_penarikan') == 'desc' ? 'selected' : '' }}>Banyak ke Kecil</option>
                                <option value="asc" {{ request('sort_penarikan') == 'asc' ? 'selected' : '' }}>Kecil ke Banyak</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-12 col-lg-1 mb-2 mb-lg-0">
                            <p class="card-title text-center" style="margin-top: -10px">Dari :</p>
                        </div>
                        <div class="col-12 col-md-12 col-lg-2 mb-2 mb-lg-0">
                            <input type="date" class="form-control" style="margin-bottom: 20px" name="start_date" value="{{ request('start_date') }}" placeholder="Tanggal Mulai">
                        </div>
                        <div class="col-12 col-md-12 col-lg-1 mb-2 mb-lg-0">
                            <p class="card-title text-center" style="margin-top: -10px">s/d :</p>
                        </div>
                        <div class="col-12 col-md-12 col-lg-2 mb-2 mb-lg-0">
                            <input type="date" class="form-control" style="margin-bottom: 20px" name="end_date" value="{{ request('end_date') }}" placeholder="Tanggal Akhir">
                        </div>
                        <div class="col-12 col-md-12 col-lg-1 mb-2 mb-lg-0">
                            <button type="submit" class="btn btn-primary w-100" style="margin-bottom: 20px">
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
                            <th colspan="1" class="text-center">No.</th>
                            <th class="text-center">Saldo Awal</th>
                            <th class="text-center">Jumlah Penarikan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Alasan</th>
                            <th class="text-center">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan as $pengajuans)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">Rp. {{ number_format($pengajuans->tabungan->saldo ?? 0 ) }}</td>
                                <td class="text-center">Rp. {{ number_format($pengajuans->jumlah_penarikan ?? 0 ) }}</td>
                                <td class="text-center">
                                    @if ($pengajuans->status == 'Pending')
                                        <span class="badge bg-warning">Pending</span>
                                        <span class="badge bg-light">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @elseif ($pengajuans->status == 'Tolak')
                                        <span class="badge bg-danger">Tolak</span>
                                        <span class="badge bg-light">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @elseif ($pengajuans->status == 'Batal' || $pengajuans->status == 'batal')
                                        <span class="badge bg-danger">Batal</span>
                                        <span class="badge bg-light">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @elseif ($pengajuans->status == 'Terima' || $pengajuans->status == 'SUCCEEDED')
                                        <span class="badge bg-success">Terima</span>
                                        <span class="badge bg-light">{{ $pengajuans->pembayaran ?? '-' }}</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                        <span class="badge bg-light">-</span>
                                    @endif
                                </td>
                                <td class="text-center">{{Str::limit($pengajuans->alasan, 30, '...')}}</td>
                                <td class="text-center">{{ $pengajuans->created_at ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Data Kosong</td>
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
                        {{ $pengajuan->appends(['perPage' => request('perPage')])->links('layout.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')

@endsection
