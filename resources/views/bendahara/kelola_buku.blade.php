@extends('layout.main')

@section('title') Kelola Buku - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Kelola Buku</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kelola Buku</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card shadow">
        <div class="card-body" style="margin-bottom: -20px">
            {{-- Alert Messages --}}
            @if(session('alert-type'))
                <div id="alert" class="alert alert-{{ session('alert-type') }} alert-dismissible fade show" role="alert">
                    <i class="bi bi-{{ session('alert-type') === 'warning' ? 'exclamation-triangle' : 'check-circle' }}"></i>
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

            {{-- Validation Errors --}}
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

            {{-- Top Buttons --}}
            <div class="d-flex justify-content-between">
                <div class="input-group">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                        Tambah Buku
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card-body pb-1 pt-3">
            <div class="table-responsive">
                <table class="table table-hover" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($buku as $bukus)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $bukus->tahun }}</td>
                                <td class="text-center">
                                    @if($bukus->status == 1)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tutup</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('bendahara.pembukuan.detail', $bukus->id) }}" class="btn btn-sm btn-info me-1">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    @if($bukus->status == 1)
                                        <div class="btn-group">
                                            {{-- <button type="button" class="btn btn-sm btn-warning me-1" 
                                                onclick="openEditModal({{ $bukus->id }}, {{ $bukus->tahun }})">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button> --}}
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmTutup({{ $bukus->id }})">
                                                <i class="bi bi-lock"></i> Tutup
                                            </button>
                                        </div>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                            <i class="bi bi-lock-fill"></i> Ditutup
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data buku.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center">
                    <form method="GET" action="{{ request()->url() }}">
                        <div class="d-flex align-items-center mb-3">
                            <label for="perPage" class="me-2">Tampilkan</label>
                            <select name="perPage" id="perPage" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                                <option value="1000" {{ request('perPage') == 1000 ? 'selected' : '' }}>Semua</option>
                            </select>
                            <span>Data</span>
                        </div>
                    </form>
                    <div>
                        {{ $buku->appends(['perPage' => request('perPage')])->links('layout.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade modal-borderless" id="tambahModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="tambahModalLabel">Tambah Buku Baru</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('bendahara.pembukuan.add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun</label>
                        <input type="number" name="tahun" id="tahun" class="form-control" required placeholder="Contoh: {{ date('Y') }}">
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="generate_naik_kelas" id="generate_naik_kelas" value="1">
                        <label class="form-check-label" for="generate_naik_kelas">
                            Generate Siswa Naik Kelas dari Tahun Sebelumnya
                        </label>
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

{{-- Modal Edit Buku --}}
<div class="modal fade modal-borderless" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Edit Tahun Buku</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_tahun" class="form-label">Tahun</label>
                        <input type="number" name="tahun" id="edit_tahun" class="form-control" required placeholder="Masukkan tahun baru">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Modal Tutup Buku --}}
<div class="modal fade" id="tutupModal" tabindex="-1" aria-labelledby="tutupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="tutupModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Konfirmasi Penutupan Buku
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <i class="bi bi-lock-fill" style="font-size: 3rem;"></i>
                </div>
                <h4 class="mb-3">Apakah Anda yakin ingin menutup buku ini?</h4>
                <p class="text-muted mb-0">Setelah ditutup, buku tidak dapat dibuka kembali.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <form id="tutupForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger px-4 py-2">
                        <i class="bi bi-lock me-1"></i> Tutup Buku
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function openEditModal(id, tahun) {
        $('#editForm').attr('action', '/bendahara/kelola-pembukuan/edit/' + id);
        $('#edit_tahun').val(tahun);
        $('#editModal').modal('show');
    }
    
    function confirmTutup(id) {
        $('#tutupForm').attr('action', '/bendahara/kelola-pembukuan/tutup/' + id);
        $('#tutupModal').modal('show');
    }

    // Optional: hide alert automatically
    document.addEventListener('DOMContentLoaded', () => {
        const alert = document.getElementById('alert');
        if (alert) {
            setTimeout(() => alert.style.display = 'none', 3000);
        }
    });
</script>
@endsection