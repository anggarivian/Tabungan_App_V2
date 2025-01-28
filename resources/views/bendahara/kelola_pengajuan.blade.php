@extends('layout.main')

@section('title') Kelola Pengajuan Penarikan - SakuRame @endsection

@section('content')
<div class="page-heading mb-2">
    <div class="d-flex justify-content-between ">
        <h3 class="mt-3">Pengajuan Penarikan</h3>
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
                <li class="breadcrumb-item active" aria-current="page">Pengajuan</li>
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
                    <p class="card-title">Data Pengajuan Penarikan Tabungan</p>
                </div>
                <form action="/bendahara/kelola-pengajuan" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control rounded" style="padding-right: 1px" name="search" id="search" value="{{ request('search') }}" placeholder="Cari..." aria-describedby="button-addon2">
                        <button class="btn btn-primary" type="submit" id="button-addon2">Cari</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body pb-1 pt-3">
            <div class="table-responsive">
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
                        @forelse ($pengajuan as $index => $pengajuans)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $pengajuans->user->username }}</td>
                                <td>{{ $pengajuans->user->name }}</td>
                                <td class="text-center">{{ $pengajuans->user->kelas->name }}</td>
                                <td class="text-center">{{ $pengajuans->alasan}}</td>
                                <td class="text-center">Rp. {{ number_format($pengajuans->jumlah_penarikan ?? 0 ) }}</td>
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
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-success" data-id="{{ $pengajuans->id }}" data-bs-toggle="modal" data-bs-target="#editModal">
                                            Proses
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Data Kosong</td>
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

{{-- Modal Edit --}}
<div class="modal fade modal-borderless" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Proses Pengajuan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="{{ route ('bendahara.pengajuan.terima') }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="edit-id">ID</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="edit-id" class="form-control" name="id"  required readonly hidden>
                            <input type="text" id="edit-username" class="form-control" name="username"  required readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-name">Nama</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="edit-name" class="form-control" name="name"  required readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-kelas">Kelas</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="edit-kelas" class="form-control" name="kelas" required readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-alasan">Alasan</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <textarea id="edit-alasan" class="form-control" name="alasan"  rows="3" required readonly></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-tabungan">Jumlah Tabungan</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="edit-tabungan" class="form-control" name="tabungan" required readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-jumlah_tarik">Jumlah Penarikan</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="number" id="edit-jumlah_tarik" class="form-control" name="jumlah_tarik" required readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-pembayaran">Pembayaran</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="edit-pembayaran" class="form-control" name="pembayaran" required readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="tolakButton" class="btn btn-light" type="button">Tolak</a>
                    <button type="submit" class="btn btn-primary">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.btn-success').on('click', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('bendahara.pengajuan.getData', '') }}/" + id,
                type: 'GET',
                success: function(response) {
                    if(response) {
                        $('#edit-id').val(response.id);
                        $('#edit-username').val(response.username);
                        $('#edit-name').val(response.name);
                        $('#edit-kelas').val(response.kelas);
                        $('#edit-jumlah_tarik').val(response.jumlah_tarik);
                        $('#edit-tabungan').val(response.tabungan);
                        $('#edit-pembayaran').val(response.pembayaran);
                        $('#edit-alasan').val(response.alasan);

                        $('#tolakButton').attr('href', '/bendahara/kelola-pengajuan/tolak/' + response.id);

                    } else {
                        alert('Gagal mengambil data');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengambil data');
                }
            });
        });
    });
    function confirmDelete(id) {
        $('#deleteForm').attr('action', '/bendahara/kelola-siswa/hapus/' + id);
        $('#deleteModal').modal('show');
    }

</script>
@endsection
