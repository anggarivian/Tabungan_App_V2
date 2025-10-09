@extends('layout.main')

@section('title') Kelola Siswa - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Kelola Siswa</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    @if(auth()->user()->roles_id == 1)
                        <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 2)
                        <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 3)
                        <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 4)
                        <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Kelola Siswa</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="card shadow">
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                        Tambah Data
                    </button>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                        Import
                    </button>
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
            <div class="table-responsive">
                <table class="table table-hover" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th>Nama</th>
                            <th class="text-center">Kode</th>
                            {{-- <th>Email</th> --}}
                            <th class="text-center">Kelas</th>
                            <th class="text-center">Jenis Kelamin</th>
                            <th class="text-center">Orang Tua</th>
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
                                <td>{{ Str::limit($users->name, 25, '...') }}</td>
                                <td class="text-center">{{ $users->username }}</td>
                                {{-- <td>{{ $users->email }}</td> --}}
                                <td class="text-center">{{ $users->kelas->name ?? '-' }}</td>
                                <td class="text-center">{{ $users->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td >{{ Str::limit($users->orang_tua, 10, '...') }}</td>
                                <td class="text-center">{{ $users->kontak }}</td>
                                <td>{{ Str::limit($users->alamat, 10, '...') }}</td>
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
                                <option value="1000" {{ request('perPage') == 100 ? 'selected' : '' }}>Semua</option>
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
                <h1 class="modal-title fs-5" id="tambahModalLabel">Tambah Data Siswa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bendahara.siswa.add') }}" method="POST">
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
                            <label for="siswa-horizontal">Kelas</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <select id="siswa-horizontal" class="form-select" name="kelas" required>
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
                        <div class="col-md-4">
                            <label for="orang-tua-horizontal">Orang Tua</label>
                        </div>
                        <div class="col-md-8 form-group">
                            <input type="text" id="orang-tua-horizontal" class="form-control" name="orang_tua" placeholder="Orang Tua" required>
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Data Siswa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- Form --}}
                <form id="editForm" method="POST" action="{{ route('bendahara.siswa.edit', '') }}">
                    @csrf
                    @method('PUT')

                    {{-- Tabs --}}
                    <ul class="nav nav-tabs" id="editTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="data-tab" data-bs-toggle="tab" data-bs-target="#dataTab" type="button" role="tab">Edit Data</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#passwordTab" type="button" role="tab">Ganti Password</button>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content p-3">
                        {{-- Tab 1: Edit Data --}}
                        <div class="tab-pane fade show active" id="dataTab" role="tabpanel">
                            <div class="row">
                                <div class="col-md-4"><label for="edit-name">Nama</label></div>
                                <div class="col-md-8 mb-2"><input type="text" id="edit-name" class="form-control" name="name" required></div>

                                <div class="col-md-4"><label for="edit-kelas">Kelas</label></div>
                                <div class="col-md-8 mb-2">
                                    <select id="edit-kelas" class="form-select" name="kelas" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}">{{ $k->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4"><label for="edit-email">Email</label></div>
                                <div class="col-md-8 mb-2"><input type="email" id="edit-email" class="form-control" name="email" required></div>

                                <div class="col-md-4"><label for="edit-jenis_kelamin">Jenis Kelamin</label></div>
                                <div class="col-md-8 mb-2">
                                    <select id="edit-jenis_kelamin" class="form-select" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>

                                <div class="col-md-4"><label for="edit-kontak">Kontak</label></div>
                                <div class="col-md-8 mb-2"><input type="text" id="edit-kontak" class="form-control" name="kontak" required></div>

                                <div class="col-md-4"><label for="edit-alamat">Alamat</label></div>
                                <div class="col-md-8 mb-2"><textarea id="edit-alamat" class="form-control" name="alamat" rows="3" required></textarea></div>

                                <div class="col-md-4"><label for="edit-orang-tua">Orang Tua</label></div>
                                <div class="col-md-8 mb-2"><input type="text" id="edit-orang_tua" class="form-control" name="orang_tua" required></div>
                            </div>
                        </div>

                        {{-- Tab 2: Ganti Password --}}
                        <div class="tab-pane fade" id="passwordTab" role="tabpanel">
                            <div class="row">
                                <div class="col-md-4"><label for="edit-password">Password Baru</label></div>
                                <div class="col-md-8 mb-2">
                                    <input type="password" id="edit-password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- Modal Konfirmasi Delete Yang Ditingkatkan --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="mb-3 text-danger">
                    <i class="bi bi-trash-fill" style="font-size: 3rem;"></i>
                    </div>
                    <h4 class="mb-3">Anda yakin ingin menghapus?</h4>
                    <p class="text-muted">
                    Perihatikan bahwa menghapus data siswa akan menghapus semua data yang terkait dengan
                    <span class="fw-bold text-danger">tabungan</span>.
                    </p>
                    <div class="alert alert-warning d-flex align-items-center mt-3">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <div>Tindakan ini tidak dapat dibatalkan setelah dilakukan.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <form id="deleteForm" method="POST" action="{{ route('bendahara.siswa.delete', '') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 py-2">
                    <i class="bi bi-trash me-1"></i> Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Import --}}
<div class="modal fade modal-borderless" id="importModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="importModalLabel">Import Data Siswa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="formFile" class="form-label">Pilih File ( Format : .xls .xlsx)</label>
                        <input class="form-control" type="file" name="file" id="formFile">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Script untuk animasi tambahan saat membuka modal
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function() {
            const trashIcon = this.querySelector('.bi-trash-fill');
            trashIcon.style.transform = 'scale(0)';
            setTimeout(() => {
            trashIcon.style.transform = 'scale(1.2)';
            setTimeout(() => {
                trashIcon.style.transform = 'scale(1)';
            }, 200);
            }, 300);
        });

        // Script untuk animasi saat form delete disubmit
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menghapus...';
            submitBtn.disabled = true;
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('.btn-warning').on('click', function() {
            var id = $(this).data('id');
            var timestamp = new Date().getTime(); // Tambahkan timestamp

            $.ajax({
                url: "{{ route('bendahara.siswa.getData', '') }}/" + id + "?t=" + timestamp,
                type: 'GET',
                success: function(response) {
                    if(response) {
                        $('#edit-name').val(response.name);
                        $('#edit-email').val(response.email);
                        $('#edit-jenis_kelamin').val(response.jenis_kelamin);
                        $('#edit-kontak').val(response.kontak);
                        $('#edit-alamat').val(response.alamat);
                        $('#edit-orang_tua').val(response.orang_tua);
                        $('#edit-kelas').val(response.kelas_id);

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
