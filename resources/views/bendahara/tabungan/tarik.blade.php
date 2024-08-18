@extends('layout.main')

@section('title') Tarik Tabungan - SakuRame @endsection

@section('content')
<div class="page-heading mb-2">
    <div class="d-flex justify-content-between">
        <h3 class="mt-3">Tarik Tabungan</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-right">
                @if(auth()->user()->roles_id == 1)
                    <li class="breadcrumb-item"><a href="{{ route ('kepsek.dashboard')}}">Dashboard</a></li>
                @elseif(auth()->user()->roles_id == 2)
                    <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                @elseif(auth()->user()->roles_id == 3)
                    <li class="breadcrumb-item"><a href="{{ route ('walikelas.bendahara')}}">Bendahara</a></li>
                @elseif(auth()->user()->roles_id == 4)
                    <li class="breadcrumb-item"><a href="{{ route ('siswa.dashboard')}}">Siswa</a></li>
                @endif
                <li class="breadcrumb-item"><a href="{{ route ('tabungan.index')}}">Tabungan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tarik</li>
            </ol>
        </nav>
    </div>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <form action="/bendahara/kelola-siswa" method="GET">
                        <div class="form-group mb-0">
                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    <div class="input-group mb-3">
                                        <label for="cari">Cari ID Tabungan</label>
                                    </div>
                                </div>
                                <div class="col-md-8 mb-1">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control " id="search" name="search" placeholder="Cari ID Tabungan" value="{{ request('search') }}" placeholder="Cari..." aria-describedby="button-addon2">
                                        <button class="btn btn-primary" type="submit" id="button-addon2">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                    <label for="nama">Nama</label>
                                </div>
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <input type="text" class="form-control" id="nama" name="nama" readnly disabled>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                    <label for="kelas">Kelas</label>
                                </div>
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <input type="text" class="form-control" id="kelas" name="kelas" readnly disabled>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                    <label for="jumlah_tabungan">Tabungan Saat Ini</label>
                                </div>
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <input type="number" class="form-control" id="jumlah_tabungan" name="jumlah_tabungan" readnly disabled>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                    <label for="jumlah_tarik">Jumlah Tarik</label>
                                </div>
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Rp.</span>
                                        <input type="number" class="form-control" id="jumlah_tarik" name="jumlah_tarik" placeholder="Masukkan Jumlah Tarik">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                </div>
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn btn-secondary" style="width: 30%">Kembali</button>
                                        <button type="submit" class="btn btn-light" style="width: 30%">Input Lagi</button>
                                        <button type="submit" class="btn btn-primary" style="width: 30%">Tambah</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <div class="container ">
                        <div class="d-flex justify-content-center mt-3">
                            <img src="{{ asset('dist/assets/compiled/jpg/2.jpg') }}" height="250px" width="250px" alt="Profile" style="border-radius: 15px;">
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
