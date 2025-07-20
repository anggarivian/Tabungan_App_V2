@extends('layout.main')

@section('title') Kelola Pengajuan Penarikan - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Pengajuan Penarikan</h3>
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
                    <li class="breadcrumb-item active" aria-current="page">Pengajuan</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="card shadow-lg">
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
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuan as $index => $pengajuans)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $pengajuans->user->username }}</td>
                                <td>{{ Str::limit($pengajuans->user->name, 20, '...') }}</td>
                                <td class="text-center">{{ $pengajuans->user->kelas->name }}</td>
                                <td class="text-center">{{ Str::limit($pengajuans->alasan, 20, '...')}}</td>
                                <td class="text-center">Rp. {{ number_format($pengajuans->jumlah_penarikan ?? 0 ) }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($pengajuans->created_at)->format('d M Y H:i') }}</td>
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

{{-- Modal --}}
<div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editModalLabel">Proses Pengajuan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editForm" method="POST" action="#">
                <div class="modal-body">
                    <div class="row g-4">

                        <!-- Kolom Kiri: Data User -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3 border-bottom pb-2">Data Siswa</h6>

                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">ID Tabungan</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" id="edit-id" class="form-control d-none" name="id" required readonly>
                                    <input type="text" id="edit-username" class="form-control-plaintext" name="username" required readonly>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">Nama</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" id="edit-name" class="form-control-plaintext" name="name" required readonly>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">Kelas</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" id="edit-kelas" class="form-control-plaintext" name="kelas" required readonly>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">Jumlah Tabungan</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" id="edit-tabungan" class="form-control-plaintext bg-warning bg-opacity-25 border border-warning rounded px-2 py-1 fw-bold" name="tabungan" required readonly>
                                </div>
                            </div>

                            <div class="row align-items-start mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">Alasan</label>
                                </div>
                                <div class="col-8">
                                    <textarea id="edit-alasan" class="form-control-plaintext" name="alasan" rows="2" required readonly></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Data Pembayaran -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3 border-bottom pb-2">Detail Pembayaran</h6>

                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">Jenis Pembayaran</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" id="edit-pembayaran" class="form-control-plaintext" name="pembayaran" required readonly>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">Jumlah Penarikan</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" id="edit-jumlah_tarik" class="form-control-plaintext bg-success bg-opacity-25 border border-success rounded px-2 py-1 text-success fw-bold" name="jumlah_tarik" required readonly>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">Metode Penarikan</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" id="edit-metode_digital" class="form-control-plaintext" name="metode_digital" required readonly>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">Tujuan Penarikan</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" id="edit-type_tujuan" class="form-control-plaintext" name="type_tujuan" required readonly>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">No. Rekening/E-Wallet</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" id="edit-nomor_tujuan" class="form-control-plaintext" name="nomor_tujuan" required readonly>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <div class="col-4">
                                    <label class="form-label mb-0 fw-medium">Biaya Admin (5%)</label>
                                </div>
                                <div class="col-8">
                                    <input type="text" id="edit-premi" class="form-control-plaintext bg-warning bg-opacity-25 border border-warning rounded px-2 py-1 text-warning fw-bold" name="premi" readonly>
                                    <small class="text-muted d-block mt-1">Biaya admin dihitung otomatis</small>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="tolakButton" class="btn btn-outline-danger">Tolak</button>
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
                        $('#edit-status').val(response.status);
                        $('#edit-alasan').val(response.alasan);
                        $('#edit-created_at').val(response.created_at);
                        // Tambahan untuk metode digital, bank atau e-wallet
                        $('#edit-metode_digital').val(response.metode_digital);
                        $('#edit-type_tujuan').val(response.type_tujuan);
                        $('#edit-nomor_tujuan').val(response.nomor_tujuan);

                        $('#tolakButton').attr('href', '/bendahara/pengajuan/tolak/' + response.id);

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
    $('#tolakButton').on('click', function () {
        var id = $('#edit-id').val();
        if (id) {
            window.location.href = '/bendahara/pengajuan/tolak/' + id;
        }
    });
    function confirmDelete(id) {
        $('#deleteForm').attr('action', '/bendahara/kelola-siswa/hapus/' + id);
        $('#deleteModal').modal('show');
    }
</script>
<script>
    function genapkanKeRibuan(angka) {
        return Math.ceil(angka / 1000) * 1000;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const jumlahTarikInput = document.getElementById('edit-jumlah_tarik');
        const premiInput = document.getElementById('edit-premi');

        const modal = document.getElementById('editModal');
        modal.addEventListener('shown.bs.modal', function () {
            const jumlahTarik = parseInt(jumlahTarikInput.value.replace(/[^0-9]/g, '')) || 0;
            const biayaAdmin = Math.ceil(jumlahTarik * 0.05);
            const dibulatkan = genapkanKeRibuan(biayaAdmin);

            premiInput.value = dibulatkan.toLocaleString('id-ID');
        });
    });
</script>

@endsection
