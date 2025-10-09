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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahRombelModal">
                    Tambah Rombel
                </button>
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
                            <th>Nama Rombel</th>
                            <th class="text-center">Tingkat</th>
                            <th class="text-center">Walikelas</th>
                            <th class="text-center">Jumlah Siswa</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td>Rombel 1</td>
                            <td class="text-center">10</td>
                            <td class="text-center">Budi</td>
                            <td class="text-center">30</td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editRombelModal" data-id="1">
                                    <i class="bi-people"></i> Anggota
                                </button>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#editRombelModal" data-id="1">
                                    <i class="bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-center">Data Kosong</td>
                        </tr>
                    </tbody>
                </table>
                {{-- <div class="d-flex justify-content-between">
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
                </div> --}}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Rombel -->
<div class="modal fade" id="tambahRombelModal" tabindex="-1" aria-labelledby="tambahRombelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahRombelModalLabel">Tambah Rombel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <form id="formTambahRombel">
          <div class="modal-body">
          <form id="formTambahRombel">
            <div class="mb-3">
              <label class="form-label">Nama Rombel</label>
              <input type="text" class="form-control" placeholder="Misal: 1A">
            </div>
            <div class="mb-3">
              <label class="form-label">Tingkat</label>
              <select class="form-select">
                <option value="1">Kelas 1</option>
                <option value="2">Kelas 2</option>
                <option value="3">Kelas 3</option>
                <option value="4">Kelas 4</option>
                <option value="5">Kelas 5</option>
                <option value="6">Kelas 6</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Wali Kelas</label>
              <select class="form-select">
                <option>Pak Andi</option>
                <option>Bu Siti</option>
                <option>Pak Budi</option>
                <option>Bu Rina</option>
              </select>
            </div>
          </form>
        </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="formTambahRombel">Simpan</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal Edit Rombel -->
<div class="modal fade modal-borderless" id="editRombelModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editRombelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editRombelLabel">Edit Rombel (Dummy Data)</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <div class="row">
          <!-- Anggota Rombel -->
          <div class="col-md-6">
            <h6>👥 Anggota Rombel</h6>
            <ul id="anggotaRombel" class="list-group min-vh-50 border rounded p-2" style="min-height: 400px; max-height: 60vh; overflow-y: auto;">
              <li class="list-group-item">Andi</li>
              <li class="list-group-item">Budi</li>
              <li class="list-group-item">Citra</li>
              <li class="list-group-item">Dewi</li>
              <li class="list-group-item">Eka</li>
            </ul>
          </div>

          <!-- Belum Masuk Rombel -->
          <div class="col-md-6">
            <h6>📋 Belum Masuk Rombel</h6>
            <ul id="belumRombel" class="list-group border rounded p-2" style="min-height: 400px; max-height: 60vh; overflow-y: auto;">
              <!-- Dummy 50 siswa -->
              <li class="list-group-item">Agus</li>
              <li class="list-group-item">Arif</li>
              <li class="list-group-item">Bagus</li>
              <li class="list-group-item">Bayu</li>
              <li class="list-group-item">Chandra</li>
              <li class="list-group-item">Dian</li>
              <li class="list-group-item">Fajar</li>
              <li class="list-group-item">Gilang</li>
              <li class="list-group-item">Hadi</li>
              <li class="list-group-item">Indra</li>
              <li class="list-group-item">Joko</li>
              <li class="list-group-item">Kiki</li>
              <li class="list-group-item">Lutfi</li>
              <li class="list-group-item">Maya</li>
              <li class="list-group-item">Nanda</li>
              <li class="list-group-item">Oka</li>
              <li class="list-group-item">Putri</li>
              <li class="list-group-item">Qori</li>
              <li class="list-group-item">Rian</li>
              <li class="list-group-item">Sinta</li>
              <li class="list-group-item">Tono</li>
              <li class="list-group-item">Umar</li>
              <li class="list-group-item">Vina</li>
              <li class="list-group-item">Wahyu</li>
              <li class="list-group-item">Xenia</li>
              <li class="list-group-item">Yani</li>
              <li class="list-group-item">Zaki</li>
              <li class="list-group-item">Aulia</li>
              <li class="list-group-item">Bella</li>
              <li class="list-group-item">Cindy</li>
              <li class="list-group-item">Dimas</li>
              <li class="list-group-item">Erlangga</li>
              <li class="list-group-item">Farah</li>
              <li class="list-group-item">Gita</li>
              <li class="list-group-item">Hana</li>
              <li class="list-group-item">Intan</li>
              <li class="list-group-item">Jihan</li>
              <li class="list-group-item">Kurnia</li>
              <li class="list-group-item">Linda</li>
              <li class="list-group-item">Mega</li>
              <li class="list-group-item">Nia</li>
              <li class="list-group-item">Olivia</li>
              <li class="list-group-item">Panca</li>
              <li class="list-group-item">Rizky</li>
              <li class="list-group-item">Syifa</li>
              <li class="list-group-item">Tasya</li>
              <li class="list-group-item">Uci</li>
              <li class="list-group-item">Vira</li>
              <li class="list-group-item">Wulan</li>
              <li class="list-group-item">Yusuf</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
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
@endsection

@section('js')
<!-- SortableJS + Animasi -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
  new Sortable(anggotaRombel, {
    group: 'rombel',
    animation: 200,  // animasi halus
    sort: true
  });

  new Sortable(belumRombel, {
    group: 'rombel',
    animation: 200,
    sort: true
  });
</script>


{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
</script> --}}
@endsection
