@extends('layout.main')

@section('title') Kelola Rombel - SakuRame @endsection

@section('content')
<style>
    /* Styling Custom Card & Hover Effect */
    .stat-card {
        border: none;
        border-radius: 1rem;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
</style>
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Kelola Rombel</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    @if(auth()->user()->roles_id == 1)
                        <li class="breadcrumb-item"><a href="{{ route('kepsek.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 2)
                        <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 3)
                        <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 4)
                        <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Kelola Rombel</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">

{{-- ================= SNIPPET CEK RENTANG PERIODE (UI) ================= --}}
@php
    $activePeriode = \App\Models\Periode::find(\App\Helpers\PeriodeHelper::id());
    $isReadOnly = true; 
    
    if($activePeriode) {
        $now = \Carbon\Carbon::now();
        $mulai = \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->startOfDay();
        $akhir = \Carbon\Carbon::parse($activePeriode->tanggal_berakhir)->endOfDay();
        
        if($now->between($mulai, $akhir)) {
            $isReadOnly = false;
        }
    }
@endphp

@if($isReadOnly)
    <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm" role="alert">
        <i class="bi bi-shield-lock-fill fs-4 me-3" style="margin-top: -25px;"></i>
        <div>
            <strong>Mode Hanya Baca!</strong><br>
            <small>Anda tidak bisa melakukan perubahan data atau transaksi dikarenakan hari ini berada di luar rentang tanggal ({{ \Carbon\Carbon::parse($activePeriode->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($activePeriode->tanggal_berakhir)->format('d M Y') }}) periode ini.</small>
        </div>
    </div>
@endif

    <div class="card stat-card shadow-sm border-0">
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
                        var alertElem = document.getElementById('alert');
                        if (alertElem) alertElem.style.display = 'none';
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahRombelModal" {{ $isReadOnly ? 'disabled' : '' }}>
                    <i class="bi bi-plus-circle"></i>    
                    Tambah Rombel
                </button>
                <form action="{{ route('bendahara.rombel.index') }}" method="GET">
                    <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
                    <div class="input-group">
                        <input type="text" class="form-control rounded" style="padding-right: 1px" name="search" id="search" value="{{ request('search') }}" placeholder="Cari..." aria-describedby="button-addon2">
                        <button class="btn btn-primary" type="submit" id="button-addon2">Cari</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body pb-1 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">
                    Menampilkan {{ $rombels->firstItem() ?? 0 }}–{{ $rombels->lastItem() ?? 0 }}
                    dari {{ $rombels->total() }} rombel
                </span>
            </div>

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
                        @forelse($rombels as $index => $rombel)
                        <tr>
                            <td class="text-center">{{ $rombels->firstItem() + $index }}</td>
                            <td>{{ $rombel->name }}</td>
                            <td class="text-center">{{ $rombel->kelas->name ?? '-' }}</td>
                            <td class="text-center">{{ $rombel->walikelas->name ?? 'Belum Ditentukan' }}</td>
                            <td class="text-center">{{ $rombel->siswa_count }} Siswa</td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-sm btn-anggota" data-id="{{ $rombel->id }}">
                                    <i class="bi-people"></i> Anggota
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $rombel->id }})" {{ $isReadOnly ? 'disabled' : '' }}>
                                    <i class="bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Data Kosong</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <form method="GET" action="{{ url()->current() }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <div class="d-flex justify-content-end mb-3 align-items-center">
                            <label for="perPage" class="me-2 mb-0">Show</label>
                            <select name="perPage" id="perPage" class="form-select form-control-sm form-select-sm" onchange="this.form.submit()">
                                <option value="10" {{ request('perPage', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </form>
                    <div class="justify-content-end">
                        {{ $rombels->links('layout.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Rombel --}}
<div class="modal fade" id="tambahRombelModal" tabindex="-1" aria-labelledby="tambahRombelModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tambahRombelModalLabel">Tambah Rombel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <form id="formTambahRombel" method="POST" action="{{ route('bendahara.rombel.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Nama Rombel</label>
            <input type="text" name="name" class="form-control" placeholder="Misal: 1A" required>
            <small class="text-muted">Kode siswa nanti mengikuti inti nama ini, misal "1A" -&gt; 1A01, 1A02, dst.</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Tingkat</label>
            <select name="kelas_id" class="form-select" required>
              <option value="">Pilih Tingkat</option>
              @foreach($list_kelas as $kelas)
                <option value="{{ $kelas->id }}">Kelas {{ $kelas->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Wali Kelas</label>
            <select name="walikelas_id" class="form-select">
              <option value="">-- Pilih Wali Kelas --</option>
              @foreach($list_walikelas as $wk)
                <option value="{{ $wk->id }}">{{ $wk->name }}</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="formTambahRombel" {{ $isReadOnly ? 'disabled' : '' }}>Simpan</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Edit Rombel & Anggota --}}
<div class="modal fade modal-borderless" id="editRombelModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editRombelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editRombelLabel">Edit Rombel & Anggota</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="formEditRombel" method="POST" action="">
          @csrf
          @method('PUT')
          <div class="row mb-4">
              <div class="col-md-4">
                  <label class="form-label">Nama Rombel</label>
                  <input type="text" name="name" id="edit-name" class="form-control" readonly required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tingkat</label>
                    <input type="text" name="kelas_id" id="edit-kelas_id" class="form-control" readonly required>
              </div>
              <div class="col-md-4">
                  <label class="form-label">Wali Kelas</label>
                  <select name="walikelas_id" id="edit-walikelas_id" class="form-select">
                      <option value="">-- Pilih Wali Kelas --</option>
                      @foreach($list_walikelas as $wk)
                        <option value="{{ $wk->id }}">{{ $wk->name }}</option>
                      @endforeach
                  </select>
              </div>
          </div>
        </form>

        <div class="row">
          <div class="col-md-6">
            <h6>👥 Anggota Rombel (Geser ke sini untuk memasukkan siswa)</h6>
            <ul id="anggotaRombel" class="list-group border rounded p-2" style="min-height: 400px; max-height: 50vh; overflow-y: auto;"></ul>
          </div>

          <div class="col-md-6">
            <h6>📋 Belum Masuk Rombel</h6>
            <ul id="belumRombel" class="list-group border rounded p-2" style="min-height: 400px; max-height: 50vh; overflow-y: auto;"></ul>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="formEditRombel" {{ $isReadOnly ? 'disabled' : '' }}>Simpan</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Konfirmasi Delete --}}
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
                        Perhatikan bahwa menghapus data rombel akan melepaskan status keanggotaan siswa di rombel ini.
                    </p>
                    <div class="alert alert-warning d-flex align-items-center mt-3">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <div>Rombel dari periode yang sudah lewat tidak bisa dihapus (data riwayat kelas siswa).</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 py-2" {{ $isReadOnly ? 'disabled' : '' }}>
                        <i class="bi bi-trash me-1"></i> Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    var anggotaEl = document.getElementById('anggotaRombel');
    var belumEl = document.getElementById('belumRombel');

    new Sortable(anggotaEl, { group: 'rombel', animation: 200, sort: true });
    new Sortable(belumEl, { group: 'rombel', animation: 200, sort: true });

    $('.btn-anggota').on('click', function() {
        var id = $(this).data('id');

        $('#formEditRombel').attr('action', "{{ route('bendahara.rombel.index') }}/" + id + "/update");

        $('#anggotaRombel').html('<li class="list-group-item text-center text-muted">Memuat data siswa...</li>');
        $('#belumRombel').html('<li class="list-group-item text-center text-muted">Memuat data siswa...</li>');

        $('#editRombelModal').modal('show');

        $.ajax({
            url: "{{ route('bendahara.rombel.index') }}/" + id + "/students",
            type: 'GET',
            cache: false,
            success: function(response) {
                $('#edit-name').val(response.rombel.name);
                $('#edit-kelas_id').val(response.rombel.kelas_id);
                $('#edit-walikelas_id').val(response.rombel.walikelas_id);

                var anggotaHtml = '';
                response.anggota.forEach(function(student) {
                    var kodeLabel = (student.pivot && student.pivot.kode) ? student.pivot.kode + ' - ' : '';
                    anggotaHtml += '<li class="list-group-item list-group-item-action" data-user-id="' + student.id + '"><i class="bi bi-person-fill me-2"></i>' + kodeLabel + student.name + '</li>';
                });
                $('#anggotaRombel').html(anggotaHtml);

                var belumHtml = '';
                if (response.belum_rombel.length === 0) {
                    belumHtml = '<li class="list-group-item text-center text-muted">Semua siswa sudah masuk rombel</li>';
                } else {
                    response.belum_rombel.forEach(function(student) {
                        belumHtml += '<li class="list-group-item list-group-item-action" data-user-id="' + student.id + '"><i class="bi bi-person me-2"></i>' + student.name + '</li>';
                    });
                }
                $('#belumRombel').html(belumHtml);
            },
            error: function() {
                alert('Gagal mengambil data rombel dari server.');
            }
        });
    });

    $('#formEditRombel').on('submit', function(e) {
        $('.dynamic-student-id').remove();

        $('#anggotaRombel li').each(function() {
            var studentId = $(this).data('user-id');
            if (studentId) {
                $('#formEditRombel').append('<input type="hidden" class="dynamic-student-id" name="student_ids[]" value="' + studentId + '">');
            }
        });
    });

    function confirmDelete(id) {
        $('#deleteForm').attr('action', "{{ route('bendahara.rombel.index') }}/" + id + "/delete");
        $('#deleteModal').modal('show');
    }

    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menghapus...';
        submitBtn.disabled = true;
    });
</script>
@endsection