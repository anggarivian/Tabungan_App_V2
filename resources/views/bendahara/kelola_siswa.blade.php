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
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form action="{{ route('bendahara.siswa.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-muted mb-1">Cari</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                            placeholder="Nama, email, kontak, orang tua, alamat...">
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="status_siswa" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status_siswa') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status_siswa') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="lulus" {{ request('status_siswa') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="pindah" {{ request('status_siswa') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="">Semua</option>
                            <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">Urutkan</label>
                        <select name="sort_by" class="form-select">
                            <option value="terbaru" {{ request('sort_by', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlama" {{ request('sort_by') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                            <option value="nama_az" {{ request('sort_by') == 'nama_az' ? 'selected' : '' }}>Nama A-Z</option>
                            <option value="nama_za" {{ request('sort_by') == 'nama_za' ? 'selected' : '' }}>Nama Z-A</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Cari</button>
                        @if(request()->anyFilled(['search', 'status_siswa', 'jenis_kelamin']))
                            <a href="{{ route('bendahara.siswa.index') }}" class="btn btn-outline-secondary" title="Reset">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
                <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body pb-1 pt-3">
            
            {{-- Alert Success --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Alert Error Validation --}}
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

            {{-- Action Buttons & Search --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
                <div class="btn-group">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                        <i class="bi bi-plus-circle"></i> Tambah Data
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-file-earmark-excel"></i> Import
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">
                    Menampilkan {{ $user->firstItem() ?? 0 }}–{{ $user->lastItem() ?? 0 }}
                    dari {{ $user->total() }} siswa
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">L/P</th>
                            <th>Orang Tua</th>
                            <th class="text-center">Kontak</th>
                            <th>Alamat</th>
                            <th class="text-center" width="220">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user as $users)
                            <tr>
                                <td class="text-center">{{ $user->firstItem() + $loop->index }}</td>
                                <td>{{ Str::limit($users->name, 25, '...') }}</td>
                                <td>{{ $users->username }}</td>
                                <td class="text-center">
                                    @php
                                        $badge = [
                                            'aktif' => 'bg-success', 'nonaktif' => 'bg-secondary',
                                            'lulus' => 'bg-primary', 'pindah' => 'bg-warning',
                                        ][$users->status_siswa] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ ucfirst($users->status_siswa) }}</span>
                                </td>
                                <td class="text-center">{{ $users->jenis_kelamin }}</td>
                                <td>{{ Str::limit($users->orang_tua, 15, '...') }}</td>
                                <td class="text-center">{{ $users->kontak }}</td>
                                <td>{{ Str::limit($users->alamat, 20, '...') }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-edit btn-sm" data-id="{{ $users->id }}">Edit</button>
                                    <form method="POST" action="{{ route('bendahara.siswa.toggleStatus', $users->id) }}" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn {{ $users->status_siswa == 'aktif' ? 'btn-secondary' : 'btn-success' }} btn-sm">
                                            {{ $users->status_siswa == 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $users->id }}, '{{ addslashes($users->name) }}')">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 text-muted"></i>
                                    Belum ada data siswa.
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
                        {{ $user->links('layout.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('bendahara.siswa.add') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bi bi-info-circle"></i> Username & tabungan otomatis terbuat. Password bawaan: 12345. Kelas diatur belakangan di menu Rombel.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="contoh@email.com" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">Pilih</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Orang Tua</label>
                            <input type="text" name="orang_tua" class="form-control" placeholder="Nama ayah/ibu/wali" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Kontak (WA)</label>
                        <input type="number" name="kontak" class="form-control" placeholder="Contoh: 081234567890" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. Contoh No. 123, Kecamatan, Kabupaten" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" id="edit-name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" id="edit-email" name="email" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select id="edit-jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Orang Tua</label>
                            <input type="text" id="edit-orang_tua" name="orang_tua" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">No. Kontak (WA)</label>
                        <input type="number" id="edit-kontak" name="kontak" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea id="edit-alamat" name="alamat" class="form-control" rows="2" required></textarea>
                    </div>

                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Password Baru <small class="text-muted">(Opsional)</small></label>
                        <input type="password" id="edit-password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin diubah">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Delete --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>
                    Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Menghapus data siswa <strong id="hapusNamaSiswa"></strong> akan menghapus
                    <strong>SELURUH riwayat tabungan, transaksi, dan pengajuan</strong> siswa ini
                    secara <strong>permanen dan tidak bisa dikembalikan</strong>.
                    Apakah Anda yakin ingin melanjutkan?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Import --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Import Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih File Excel (.xls, .xlsx)</label>
                        <input class="form-control" type="file" name="file" required accept=".xls,.xlsx">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnBatal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-success" id="btnImport">
                        <span class="btn-text">
                            <i class="bi bi-upload"></i> Proses Import
                        </span>

                        <span class="btn-loading d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Mengimpor...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.getElementById('importForm').addEventListener('submit', function () {

    const btnImport = document.getElementById('btnImport');
    const btnBatal = document.getElementById('btnBatal');

    // Disable tombol
    btnImport.disabled = true;
    btnBatal.disabled = true;

    // Ganti teks menjadi loading
    btnImport.querySelector('.btn-text').classList.add('d-none');
    btnImport.querySelector('.btn-loading').classList.remove('d-none');
});
</script>
<script>
    $(document).ready(function() {
        $('.btn-edit').on('click', function() {
            var id = $(this).data('id');
            var timestamp = new Date().getTime();

            $.ajax({
                url: "{{ url('bendahara/kelola-siswa/data') }}/" + id + "?t=" + timestamp,
                type: 'GET',
                success: function(response) {
                    if (response) {
                        $('#edit-name').val(response.name);
                        $('#edit-email').val(response.email);
                        $('#edit-jenis_kelamin').val(response.jenis_kelamin);
                        $('#edit-kontak').val(response.kontak);
                        $('#edit-alamat').val(response.alamat);
                        $('#edit-orang_tua').val(response.orang_tua);
                        $('#edit-password').val('');

                        $('#editForm').attr('action', "{{ url('bendahara/kelola-siswa/edit') }}/" + id);
                        $('#editModal').modal('show');
                    } else {
                        alert('Gagal mengambil data');
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengambil data dari server');
                }
            });
        });
    });

    function confirmDelete(id, nama) {
        $('#hapusNamaSiswa').text(nama);
        $('#deleteForm').attr('action', "{{ url('bendahara/kelola-siswa/hapus') }}/" + id);
        $('#deleteModal').modal('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function() {
                const icon = this.querySelector('.bi-exclamation-triangle-fill'); // fix: icon yang benar-benar ada di HTML
                if (icon) {
                    icon.style.transform = 'scale(0)';
                    setTimeout(() => {
                        icon.style.transform = 'scale(1.2)';
                        setTimeout(() => { icon.style.transform = 'scale(1)'; }, 200);
                    }, 300);
                }
            });
        }

        const deleteForm = document.getElementById('deleteForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menghapus...';
                btn.disabled = true;
            });
        }
    });
</script>
@endsection