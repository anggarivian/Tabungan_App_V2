@extends('layout.main')

@section('title') Kelola Walikelas - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Kelola Walikelas</h3>
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
                    <li class="breadcrumb-item active" aria-current="page">Kelola Walikelas</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    <div class=" card shadow-lg">
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
                <div class="alert alert-danger alert-dismissible fade show pb-0" role="alert">
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
                    <button type="button" class="btn  btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                        Tambah Data
                    </button>
                </div>
                <form action="/bendahara/kelola-walikelas" method="GET">
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
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th class="text-center">Kelas</th>
                            <th class="text-center">Kontak</th>
                            <th>Alamat</th>
                            {{-- <th class="text-center">Tanggal</th> --}}
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user as $users)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ Str::limit($users->name, 20, '...') }}</td>
                                <td>{{ $users->username }}</td>
                                <td>{{ Str::limit($users->email, 20, '...') }}</td>
                                <td class="text-center">{{ $users->kelas->name ?? '-' }}</td>
                                <td class="text-center">{{ $users->kontak }}</td>
                                <td>{{ Str::limit($users->alamat, 20, '...') }}</td>
                                {{-- <td class="text-center">{{ \Carbon\Carbon::parse($users->created_at)->format('d M Y H:i') }}</td> --}}
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-warning" data-id="{{ $users->id }}" data-bs-toggle="modal" data-bs-target="#editModal">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-id="{{ $users->id }}" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="confirmDelete({{ $users->id }})">
                                            <i class="bi bi-trash"></i>
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
                        {{ $user->appends(['perPage' => request('perPage')])->links('layout.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade modal-borderless" id="tambahModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="tambahModalLabel">Tambah Data Walikelas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bendahara.walikelas.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="name-horizontal">Nama</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="name-horizontal" class="form-control" name="name" placeholder="Nama" required>
                        </div>
                        <div class="col-md-4">
                            <label for="username-horizontal">Username</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="username-horizontal" class="form-control" name="username" placeholder="Username" required>
                        </div>
                        <div class="col-md-4">
                            <label for="walikelas-horizontal">Wali kelas</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <select id="walikelas-horizontal" class="form-select" name="kelas" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="email-horizontal">Email</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="email" id="email-horizontal" class="form-control" name="email" placeholder="Email" required>
                        </div>
                        <div class="col-md-4">
                            <label for="password-horizontal">Password</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="password" id="password-horizontal" class="form-control" name="password" placeholder="Password" required>
                        </div>
                        <div class="col-md-4">
                            <label for="gender-horizontal">Jenis Kelamin</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <select id="gender-horizontal" class="form-select" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="contact-info-horizontal">Kontak</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="contact-info-horizontal" class="form-control" name="kontak" placeholder="Kontak" required>
                        </div>
                        <div class="col-md-4">
                            <label for="address-horizontal">Alamat</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <textarea id="address-horizontal" class="form-control" name="alamat" placeholder="Alamat" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade modal-borderless" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Data Walikelas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="{{ route('bendahara.walikelas.edit', '') }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
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
                            <select id="edit-kelas" class="form-select" name="kelas" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-username">Username</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="edit-username" class="form-control" name="username" placeholder="Username" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-email">Email</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="email" id="edit-email" class="form-control" name="email" placeholder="Email" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-jenis_kelamin">Jenis Kelamin</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <select id="edit-jenis_kelamin" class="form-select" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-kontak">Kontak</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="edit-kontak" class="form-control" name="kontak" placeholder="Kontak" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit-alamat">Alamat</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <textarea id="edit-alamat" class="form-control" name="alamat" placeholder="Alamat" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Delete --}}
<div class="modal fade modal-borderless" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action="{{ route('bendahara.walikelas.delete', '') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
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
                url: "{{ route('bendahara.walikelas.getData', '') }}/" + id,
                type: 'GET',
                success: function(response) {
                    if(response) {
                        $('#edit-name').val(response.name);
                        $('#edit-username').val(response.username);
                        $('#edit-email').val(response.email);
                        $('#edit-jenis_kelamin').val(response.jenis_kelamin);
                        $('#edit-kontak').val(response.kontak);
                        $('#edit-alamat').val(response.alamat);
                        $('#edit-kelas').val(response.kelas_id);

                        $('#editModal form').attr('action', "{{ route('bendahara.walikelas.edit', '') }}/" + id);
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
        $('#deleteForm').attr('action', '/bendahara/kelola-walikelas/hapus/' + id);
        $('#deleteModal').modal('show');
    }
</script>
@endsection
