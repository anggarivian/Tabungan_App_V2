@extends('layout.main')

@section('title') Kelola Rombel - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h3 class="mt-3">Kelola Rombel</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kelola Rombel</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card shadow">
        <div class="card-body">
            @if(session('alert-message'))
                <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show">
                    {{ session('alert-message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahRombelModal">Tambah Rombel</button>
                <form action="{{ url('/bendahara/kelola-siswa') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" value="{{ request('search') }}" placeholder="Cari...">
                    <button class="btn btn-primary">Cari</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Rombel</th>
                            <th class="text-center">Tingkat</th>
                            <th class="text-center">Walikelas</th>
                            <th class="text-center">Jumlah Siswa</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rombels as $i => $r)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $r->name }}</td>
                                <td class="text-center">{{ $r->kelas->name ?? '-' }}</td>
                                <td class="text-center">{{ $r->walikelas->name ?? $r->autoWalikelas->name ?? '-' }}</td>
                                <td class="text-center">{{ $r->total_siswa }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary btn-anggota" data-id="{{ $r->id }}">
                                        <i class="bi-people"></i> Anggota
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $r->id }})">
                                        <i class="bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">Belum ada rombel.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahRombelModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('bendahara.rombel.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Tambah Rombel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Rombel</label>
          <input type="text" name="name" class="form-control" required placeholder="Misal: 1A">
        </div>
        <div class="mb-3">
          <label class="form-label">Tingkat</label>
          <select name="kelas_id" class="form-select" required>
            <option value="">-- Pilih Kelas --</option>
            @foreach($kelas as $k)
              <option value="{{ $k->id }}">Kelas {{ $k->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Wali Kelas</label>
          <select name="walikelas_id" class="form-select">
            <option value="">-- Pilih Walikelas --</option>
            @foreach($walikelas as $w)
              <option value="{{ $w->id }}">{{ $w->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Anggota -->
<div class="modal fade" id="editRombelModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editRombelLabel">Edit Rombel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6>👥 Anggota Rombel</h6>
            <ul id="anggotaRombel" class="list-group border rounded p-2" style="min-height: 300px;"></ul>
          </div>
          <div class="col-md-6">
            <h6>📋 Belum Masuk Rombel</h6>
            <ul id="belumRombel" class="list-group border rounded p-2" style="min-height: 300px;"></ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="button" id="btnSaveAnggota" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let currentRombelId = null;
let anggotaSortable, belumSortable;

$('.btn-anggota').on('click', function () {
    const id = $(this).data('id');
    currentRombelId = id;

    $.get(`/bendahara/kelola-rombel/${id}`, function (data) {
        console.log('📦 Data rombel diterima:', data);

        $('#editRombelLabel').text(`Edit Rombel ${data.rombel.name}`);
        $('#anggotaRombel').empty();
        $('#belumRombel').empty();

        // tampilkan list anggota dan belum rombel
        data.anggota.forEach(u => $('#anggotaRombel').append(`<li class="list-group-item" data-id="${u.id}">${u.name}</li>`));
        data.belumRombel.forEach(u => $('#belumRombel').append(`<li class="list-group-item" data-id="${u.id}">${u.name}</li>`));

        // Buat sortable (drag-drop)
        anggotaSortable = new Sortable(document.getElementById('anggotaRombel'), {
            group: 'rombel',
            animation: 150,
            onAdd: function (evt) {
                console.log('✅ Ditambahkan ke Anggota Rombel:', $(evt.item).text());
            },
            onRemove: function (evt) {
                console.log('❌ Dikeluarkan dari Anggota Rombel:', $(evt.item).text());
            }
        });

        belumSortable = new Sortable(document.getElementById('belumRombel'), {
            group: 'rombel',
            animation: 150,
            onAdd: function (evt) {
                console.log('✅ Ditambahkan ke Belum Rombel:', $(evt.item).text());
            },
            onRemove: function (evt) {
                console.log('❌ Dikeluarkan dari Belum Rombel:', $(evt.item).text());
            }
        });

        $('#editRombelModal').modal('show');
    });
});

$('#btnSaveAnggota').on('click', function () {
    if (!currentRombelId) return;

    const ids = [];
    $('#anggotaRombel li').each(function () {
        ids.push($(this).data('id'));
    });

    console.log('🧩 Data yang akan dikirim:', ids);

    $.post(`/bendahara/kelola-rombel/${currentRombelId}/update-anggota`, {
        _token: '{{ csrf_token() }}',
        anggota: ids
    })
    .done(function (res) {
        console.log('✅ Response dari server:', res);
        alert('Data berhasil disimpan!');
        location.reload();
    })
    .fail(function (xhr) {
        console.error('❌ Gagal menyimpan:', xhr.responseText);
        alert('Terjadi kesalahan saat menyimpan data!');
    });
});

function confirmDelete(id) {
    if (confirm('Yakin ingin menghapus rombel ini?')) {
        $.post(`/bendahara/kelola-rombel/${id}`, {
            _token: '{{ csrf_token() }}',
            _method: 'DELETE'
        }, function () {
            location.reload();
        });
    }
}
</script>
@endsection
