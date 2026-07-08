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
    <div class="card shadow">
        <div class="card-body pb-1 pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    <strong>Terjadi Kesalahan!</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                        <i class="bi bi-plus-circle"></i> Tambah Data
                    </button>
                </div>
                
                <div>
                    <form action="{{ route('bendahara.walikelas.index') }}" method="GET" class="d-flex">
                        <div class="input-group shadow-sm">
                            <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari walikelas...">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">
                    Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
                    dari {{ $users->total() }} data
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center" width="60">No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th class="text-center">Kontak</th>
                            <th>Alamat</th>
                            <th class="text-center" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $item)
                            <tr>
                                <td class="text-center">{{ $users->firstItem() + $loop->index }}</td>
                                <td>{{ Str::limit($item->name, 30, '...') }}</td>
                                <td>{{ $item->username }}</td>
                                <td>{{ $item->email }}</td>
                                <td class="text-center">{{ $item->kontak }}</td>
                                <td>{{ Str::limit($item->alamat, 30, '...') }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="{{ $item->id }}">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $item->id }})">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 text-muted"></i>
                                    Belum ada data walikelas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <form method="GET" action="{{ url()->current() }}" id="perPageForm">
                        @foreach (request()->except(['perPage', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach

                        <div class="d-flex align-items-center mb-3">
                            <label for="perPage" class="me-2 mb-0">Show</label>
                            <select name="perPage" id="perPage" class="form-select form-control-sm form-select-sm" style="width: auto" onchange="this.form.submit()">
                                @foreach ([10, 25, 50, 75, 100] as $option)
                                    <option value="{{ $option }}" {{ request('perPage', 10) == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <div>
                        {{ $users->links('layout.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade modal-borderless" id="tambahModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title">Tambah Data Walikelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bendahara.walikelas.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label for="name" class="col-form-label">Nama</label></div>
                        <div class="col-md-8"><input type="text" id="name" class="form-control" name="name" required></div>
                        
                        <div class="col-md-4"><label for="username" class="col-form-label">Username</label></div>
                        <div class="col-md-8"><input type="text" id="username" class="form-control" name="username" required></div>
                        
                        <div class="col-md-4"><label for="email" class="col-form-label">Email</label></div>
                        <div class="col-md-8"><input type="email" id="email" class="form-control" name="email" required></div>
                        
                        <div class="col-md-4"><label for="password" class="col-form-label">Password</label></div>
                        <div class="col-md-8"><input type="password" id="password" class="form-control" name="password" required></div>
                        
                        <div class="col-md-4"><label for="gender" class="col-form-label">Jenis Kelamin</label></div>
                        <div class="col-md-8">
                            <select id="gender" class="form-select" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4"><label for="kontak" class="col-form-label">Kontak</label></div>
                        <div class="col-md-8"><input type="text" id="kontak" class="form-control" name="kontak" required></div>
                        
                        <div class="col-md-4"><label for="alamat" class="col-form-label">Alamat</label></div>
                        <div class="col-md-8"><textarea id="alamat" class="form-control" name="alamat" rows="3" required></textarea></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade modal-borderless" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title">Edit Data Walikelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label for="edit-name" class="col-form-label">Nama</label></div>
                        <div class="col-md-8"><input type="text" id="edit-name" class="form-control" name="name" required></div>
                        
                        <div class="col-md-4"><label for="edit-username" class="col-form-label">Username</label></div>
                        <div class="col-md-8"><input type="text" id="edit-username" class="form-control" name="username" required></div>
                        
                        <div class="col-md-4"><label for="edit-email" class="col-form-label">Email</label></div>
                        <div class="col-md-8"><input type="email" id="edit-email" class="form-control" name="email" required></div>

                        <div class="col-md-4"><label for="edit-password" class="col-form-label">Password <br><small class="text-muted">(opsional)</small></label></div>
                        <div class="col-md-8"><input type="password" id="edit-password" class="form-control" name="password" placeholder="Kosongkan jika tidak diubah"></div>
                        
                        <div class="col-md-4"><label for="edit-jenis_kelamin" class="col-form-label">Jenis Kelamin</label></div>
                        <div class="col-md-8">
                            <select id="edit-jenis_kelamin" class="form-select" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4"><label for="edit-kontak" class="col-form-label">Kontak</label></div>
                        <div class="col-md-8"><input type="text" id="edit-kontak" class="form-control" name="kontak" required></div>
                        
                        <div class="col-md-4"><label for="edit-alamat" class="col-form-label">Alamat</label></div>
                        <div class="col-md-8"><textarea id="edit-alamat" class="form-control" name="alamat" rows="3" required></textarea></div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Delete --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus data walikelas ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
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
        $('.btn-edit').on('click', function() {
            var id = $(this).data('id');
            var timestamp = new Date().getTime();

            $.ajax({
                url: "{{ url('bendahara/kelola-walikelas/data') }}/" + id + "?t=" + timestamp,
                type: 'GET',
                success: function(response) {
                    if(response) {
                        $('#edit-name').val(response.name);
                        $('#edit-username').val(response.username);
                        $('#edit-email').val(response.email);
                        $('#edit-jenis_kelamin').val(response.jenis_kelamin);
                        $('#edit-kontak').val(response.kontak);
                        $('#edit-alamat').val(response.alamat);
                        $('#edit-password').val(''); 

                        $('#editForm').attr('action', "{{ url('bendahara/kelola-walikelas/edit') }}/" + id);
                        $('#editModal').modal('show');
                    } else {
                        alert('Gagal mengambil data.');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengambil data dari server.');
                }
            });
        });
    });

    function confirmDelete(id) {
        $('#deleteForm').attr('action', "{{ url('bendahara/kelola-walikelas/hapus') }}/" + id);
        $('#deleteModal').modal('show');
    }
</script>
@endsection