@extends('layout.main')

@section('title') Tarik Tabungan - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Tarik Tabungan</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    @if(auth()->user()->roles_id == 1)
                        <li class="breadcrumb-item"><a href="{{ route ('kepsek.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 2)
                        <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 3)
                        <li class="breadcrumb-item"><a href="{{ route ('walikelas.bendahara')}}">Bendahara</a></li>
                    @elseif(auth()->user()->roles_id == 4)
                        <li class="breadcrumb-item"><a href="{{ route ('siswa.dashboard')}}">Siswa</a></li>
                    @endif
                    <li class="breadcrumb-item"><a href="{{ route ('bendahara.tabungan.index')}}">Tabungan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tarik</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
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
                    <form id="searchForm">
                        <div class="form-group mb-0">
                            <div class="row">
                                <div class="col-md-4 mb-1">
                                    <div class="input-group mb-3">
                                        <label for="username" class="form-label">Cari ID Tabungan</label>
                                    </div>
                                </div>
                                <div class="col-md-8 mb-1">
                                    <div class="input-group mb-3">
                                        <input type="text" id="username" class="form-control" placeholder="Masukkan ID Tabungan">
                                        <button class="btn btn-primary" type="submit">Cari</button>
                                    </div>
                                </div>
                                <div id="tidak-ada"></div>
                            </div>
                        </div>
                    </form>
                    <form action="{{ route ('bendahara.tabungan.tarikTabungan')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                    <label for="nama">Nama</label>
                                </div>
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <input type="text" hidden class="form-control" id="username2" name="username" readonly>
                                    <input type="text" class="form-control" id="name" name="name" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                    <label for="kelas">Kelas</label>
                                </div>
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <input type="text" class="form-control" id="kelas" name="kelas" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                                    <label for="jumlah_tabungan">Tabungan Saat Ini</label>
                                </div>
                                <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">Rp.</span>
                                        <input type="number" class="form-control" id="jumlah_tabungan" name="jumlah_tabungan" readonly >
                                    </div>
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
                                        <span class="input-group-text">Rp.</span>
                                        <input type="number" class="form-control" id="jumlah_tarik" name="jumlah_tarik" placeholder="Masukkan Jumlah Tarik" >
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
                                        <a href="{{ route ('bendahara.tabungan.index')}}" type="button" class="btn btn-secondary" style="width: 48%">Kembali</a>
                                        <button type="submit" class="btn btn-primary" style="width: 48%">Tarik</button>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();

            var username = $('#username').val();

            $.ajax({
                url: "{{ route('bendahara.search') }}",
                method: "GET",
                data: { username: username },
                success: function(data) {
                    if (data) {
                        console.log(data);
                        if(data.name !== 'Tidak Ada' && data.kelas !== 'Tidak Ada' && data.tabungan !== 'Tidak Ada') {
                            $('#name').val(data.name);
                            $('#kelas').val(data.kelas);
                            $('#jumlah_tabungan').val(data.tabungan);
                        } else {
                            $('#tidak-ada').html('<div class="alert alert-danger">Data tidak ditemukan</div>');
                            setTimeout(function() {
                                $('#tidak-ada').empty();
                            }, 2000);
                            $('#name').val('');
                            $('#kelas').val('');
                            $('#jumlah_tabungan').val('');
                        }
                        $('#jumlah_tarik').focus();
                    } else {
                        alert('User tidak ditemukan');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan: ' + error);
                }
            });
        });

        $('#username').on('keypress', function(e) {
            if(e.which === 13) {
                $('#jumlah_tarik').focus();
            }
        });
    });

    $('#username').on('change', function() {
        $('#username2').val($(this).val());
    });

    $(document).ready(function(){
        setTimeout(function(){
            $('#username').focus();
        }, 500);
    });
</script>
@endsection
