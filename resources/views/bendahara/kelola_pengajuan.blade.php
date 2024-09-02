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
                        @foreach ($pengajuan as $pengajuans)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $pengajuans->username }}</td>
                                <td>{{ $pengajuans->name }}</td>
                                <td class="text-center">{{ $pengajuans->kelas->name }}</td>
                                <td class="text-center">{{ $pengajuans->saldo}}</td>
                                <td class="text-center">{{ $pengajuans->pengajuan->jumlah_penarikan ?? '-' }}</td>
                                <td class="text-center">{{ $pengajuans->pengajuan->status ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-success" data-id="{{ $pengajuans->id }}" data-bs-toggle="modal" data-bs-target="#editModal">
                                            Proses !
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $pengajuan->links('layout.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade modal-borderless" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Data Siswa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="edit-id">ID</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="edit-id" class="form-control" name="id" placeholder="ID" required readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-name">Nama</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="edit-name" class="form-control" name="name" placeholder="Nama" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-kelas">Kelas</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="edit-kelas" class="form-control" name="kelas" placeholder="Kelas" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-alasan">Alasan</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <textarea id="edit-alasan" class="form-control" name="alasan" placeholder="Alasan" rows="3" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-jumlah_penarikan">Jumlah Penarikan</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="number" id="edit-jumlah_penarikan" class="form-control" name="jumlah_penarikan" placeholder="Jumlah Penarikan" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-status">Status</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <select id="edit-status" class="form-select" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="Diterima">Diterima</option>
                                <option value="Ditolak">Ditolak</option>
                                <option value="Diproses">Diproses</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tolak</button>
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
        $('.btn-warning').on('click', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('bendahara.siswa.getData', '') }}/" + id,
                type: 'GET',
                success: function(response) {
                    if(response) {
                        $('#edit-id').val(response.id);
                        $('#edit-username').val(response.username);
                        $('#edit-name').val(response.name);
                        $('#edit-email').val(response.email);
                        $('#edit-jumlah_penarikan').val(response.jumlah_penarikan);
                        $('#edit-alasan').val(response.alasan);
                        $('#edit-status').val(response.status);

                        $('#editModal form').attr('action', "{{ route('bendahara.siswa.edit', '') }}/" + id);
                        $('#editModal').modal('show');
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
