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
                                <div class=" col-6 col-md-2 col-sm-3 ">
                                    <div class="stats-icon green mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-140 -140 800 800"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M352 96l64 0c17.7 0 32 14.3 32 32l0 256c0 17.7-14.3 32-32 32l-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l64 0c53 0 96-43 96-96l0-256c0-53-43-96-96-96l-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32zm-9.4 182.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L242.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/></svg>
                                    </div>
                                </div>
                                <div class=" col-6 col-md-4 col-sm-3">
                                    <h6 class="text-muted font-semibold">Uang Masuk</h6>
                                    <h6 class="font-extrabold mb-0">Rp. 183.000</h6>
                                </div>
                                <div class=" col-6 col-md-2 col-sm-3 ">
                                    <div class="stats-icon red mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-140 -140 800 800"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128zM160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0z"/></svg>
                                    </div>
                                </div>
                                <div class=" col-6 col-md-4 col-sm-3">
                                    <h6 class="text-muted font-semibold">Uang Keluar</h6>
                                    <h6 class="font-extrabold mb-0">Rp. 183.000</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-12 col-sm-12 ">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-6 col-md-3 col-sm-3 d-flex justify-content-start ">
                                    <div class="stats-icon blue mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-110 -130 800 800"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M64 0C28.7 0 0 28.7 0 64L0 416c0 35.3 28.7 64 64 64l16 0 16 32 64 0 16-32 224 0 16 32 64 0 16-32 16 0c35.3 0 64-28.7 64-64l0-352c0-35.3-28.7-64-64-64L64 0zM224 320a80 80 0 1 0 0-160 80 80 0 1 0 0 160zm0-240a160 160 0 1 1 0 320 160 160 0 1 1 0-320zM480 221.3L480 336c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-114.7c-18.6-6.6-32-24.4-32-45.3c0-26.5 21.5-48 48-48s48 21.5 48 48c0 20.9-13.4 38.7-32 45.3z"/></svg>
                                    </div>
                                </div>
                                <div class="col-6 col-md-9 col-sm-3">
                                    <h6 class="text-muted font-semibold">Total Jumlah</h6>
                                    <h6 class="font-extrabold mb-0">Rp. 9.231.000</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-3 col-md-12 col-sm-12 ">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-6 col-md-3 col-sm-3 d-flex justify-content-start ">
                                    <div class="stats-icon blue mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-110 -130 800 800"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M64 0C28.7 0 0 28.7 0 64L0 416c0 35.3 28.7 64 64 64l16 0 16 32 64 0 16-32 224 0 16 32 64 0 16-32 16 0c35.3 0 64-28.7 64-64l0-352c0-35.3-28.7-64-64-64L64 0zM224 320a80 80 0 1 0 0-160 80 80 0 1 0 0 160zm0-240a160 160 0 1 1 0 320 160 160 0 1 1 0-320zM480 221.3L480 336c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-114.7c-18.6-6.6-32-24.4-32-45.3c0-26.5 21.5-48 48-48s48 21.5 48 48c0 20.9-13.4 38.7-32 45.3z"/></svg>
                                    </div>
                                </div>
                                <div class="col-6 col-md-9 col-sm-3">
                                    <h6 class="text-muted font-semibold">Total Jumlah</h6>
                                    <h6 class="font-extrabold mb-0">Rp. 9.231.000</h6>
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
                        <a href="{{ route ('bendahara.tabungan.stor')}}" class="btn btn-lg btn-primary w-100 m-1 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/></svg>
                            Stor</a>
                        <a href="{{ route ('bendahara.tabungan.tarik')}}" class="btn btn-lg btn-secondary w-100 m-1 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/></svg>
                            Tarik</a>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Transaksi</h5>
                    <div class="row">
                        <div class="col-12">
                            <div class="btn-group w-100">
                                <button class="btn btn-secondary" onclick="showTable(1)">Kelas 1 A</button>
                                <button class="btn btn-secondary" onclick="showTable(2)">Kelas 2 B</button>
                                <button class="btn btn-secondary" onclick="showTable(3)">Kelas 3 C</button>
                                <button class="btn btn-secondary" onclick="showTable(4)">Kelas 4 D</button>
                                <button class="btn btn-secondary" onclick="showTable(5)">Kelas 5 E</button>
                                <button class="btn btn-secondary" onclick="showTable(6)">Kelas 6 F</button>
                                <button class="btn btn-secondary" onclick="showTable(7)">Kelas 7 G</button>
                                <button class="btn btn-secondary" onclick="showTable(8)">Kelas 8 H</button>
                                <button class="btn btn-secondary" onclick="showTable(9)">Kelas 9 I</button>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-table mt-3">
                        <!-- Tempat untuk menampilkan tabel -->
                        <div id="table1" class="table-container">
                            <table border="1">
                                <tr>
                                    <th>a 1</th>
                                    <th>a 2</th>
                                </tr>
                                <tr>
                                    <td>a 1</td>
                                    <td>a 2</td>
                                </tr>
                            </table>
                        </div>

                        <div id="table2" class="table-container">
                            <table border="1">
                                <tr>
                                    <th>Column 1</th>
                                    <th>Column 2</th>
                                </tr>
                                <tr>
                                    <td>Data 1</td>
                                    <td>Data 2</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function showTable(tableNumber) {
        // Sembunyikan semua tabel terlebih dahulu
        for (let i = 1; i <= 9; i++) {
            if (document.getElementById(`table${i}`)) {
                document.getElementById(`table${i}`).style.display = 'none';
            }
        }
        // Tampilkan tabel yang sesuai
        document.getElementById(`table${tableNumber}`).style.display = 'block';
    }
</script>

@endsection
