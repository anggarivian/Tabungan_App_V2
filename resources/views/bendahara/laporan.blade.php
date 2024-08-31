@extends('layout.main')

@section('title') Laporan - SakuRame @endsection

@section('content')
<div class="page-heading mb-2">
    <div class="d-flex justify-content-between ">
        <h3 class="mt-3">Laporan Transaksi</h3>
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
                <li class="breadcrumb-item active" aria-current="page">Laporan</li>
            </ol>
        </nav>
    </div>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-body" style="margin-bottom: -20px">
            @if(session('success'))
                <div id="alert" class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i>
                    {{ session('alert-message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <script>
                @if(session('alert-duration'))
                    setTimeout(function() {
                        document.getElementById('alert').style.display = 'none';
                    }, {{ session('alert-duration') }});
                @endif
            </script>
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show pb-1" role="alert">
                    <strong>Terjadi Kesalahan!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="d-flex justify-content-between">
                <div class="input-group">
                    {{-- <p class="card-title">Data Pengajuan Penarikan Tabungan</p> --}}
                </div>
                <form action="/bendahara/kelola-siswa" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control rounded" style="padding-right: 1px" name="search" id="search" value="{{ request('search') }}" placeholder="Cari..." aria-describedby="button-addon2">
                        <button class="btn btn-primary" type="submit" id="button-addon2">Cari</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body pb-1 pt-3">
            <div class="table-responsive-lg">
                <table class="table table-hover" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">ID</th>
                            <th>Nama</th>
                            <th class="text-center">Kelas</th>
                            <th class="text-center">Alasan</th>
                            <th class="text-center">Jumlah Penarikan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $users)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $users->username }}</td>
                                <td>{{ $users->name }}</td>
                                <td class="text-center">{{ $users->kelas->name ?? '-' }}</td>
                                <td class="text-center">{{ $users->tabungan ?? '-' }}</td>
                                <td class="text-center">{{ $users->pengajuan->jumlah_penarikan ?? '-' }}</td>
                                <td class="text-center">{{ $users->pengajuan->status ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-success" data-id="{{ $users->id }}" data-bs-toggle="modal" data-bs-target="#editModal">
                                            Proses !
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
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
