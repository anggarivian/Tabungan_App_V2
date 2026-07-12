@extends('layout.main')

@section('title') Periode - SakuRame @endsection

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
            <h3 class="mt-3">Periode</h3>
        </div>
        <div class="col-12 col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-start justify-content-md-end mb-0">
                    @if(auth()->user()->roles_id == 1)
                        <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 2)
                        <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 3)
                        <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->roles_id == 4)
                        <li class="breadcrumb-item"><a href="{{ route('bendahara.dashboard') }}">Dashboard</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">Periode</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card stat-card shadow-sm border-0">
        <div class="card-body pb-1 pt-3">
            <div class="page-content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    {{ session('error') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif
            <div class="mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPeriodeModal">
                    <i class="bi bi-plus-circle"></i>
                    Tambah Periode
                </button>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small">
                    Menampilkan {{ $periodes->firstItem() ?? 0 }}–{{ $periodes->lastItem() ?? 0 }}
                    dari {{ $periodes->total() }} periode
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="text-center" width="60">No</th>
                            <th>Periode</th>
                            <th class="text-center">Dari Tanggal</th>
                            <th class="text-center">Sampai Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="260">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($periodes as $periode)
                            <tr>
                                <td class="text-center">{{ $periodes->firstItem() + $loop->index }}</td>
                                <td>
                                    {{ $periode->nama }}
                                </td>
                                <td class="text-center">{{ $periode->tanggal_mulai }}</td>
                                <td class="text-center">{{ $periode->tanggal_selesai }}</td>
                                <td class="text-center">
                                    @if($periode->status)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(!$periode->status)
                                        <button
                                            type="button"
                                            class="btn btn-success btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#konfirmasiAktifkanModal"
                                            data-action="{{ route('bendahara.periode.aktifkan', $periode->id) }}"
                                            data-nama="{{ $periode->nama }}">
                                            Aktifkan
                                        </button>
                                    @endif

                                    <button
                                        type="button"
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editPeriodeModal"
                                        data-id="{{ $periode->id }}"
                                        data-nama="{{ $periode->nama }}"
                                        data-mulai="{{ $periode->tanggal_mulai }}"
                                        data-selesai="{{ $periode->tanggal_selesai }}">
                                        Edit
                                    </button>

                                    <button type="button" class="btn btn-secondary btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rekapModal"
                                            data-nama="{{ $periode->nama }}"
                                            data-id="{{ $periode->id }}">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Rekap
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 text-muted"></i>
                                    Belum ada data periode.
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
                            <select name="perPage" id="perPage" class="form-select form-control-sm form-select-sm"
                                style="width: auto" onchange="this.form.submit()">
                                @foreach ([10, 25, 50, 75, 100] as $option)
                                    <option value="{{ $option }}" {{ request('perPage', 10) == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <div>
                        {{ $periodes->links('layout.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Periode --}}
<div class="modal fade" id="tambahPeriodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('periode.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Periode</label>
                        <input type="text" name="nama" class="form-control" placeholder="2026 / 2027">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control">
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

{{-- Modal Konfirmasi Aktifkan Periode --}}
<div class="modal fade" id="konfirmasiAktifkanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                    Konfirmasi Aktifkan Periode
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Mengaktifkan periode <strong id="konfirmasiNamaPeriode"></strong> akan menonaktifkan
                    periode yang sedang aktif saat ini. Apakah Anda yakin ingin melanjutkan?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="konfirmasiAktifkanForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">Ya, Aktifkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editPeriodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="editPeriodeForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Periode</label>
                        <input type="text" id="editNama" name="nama" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" id="editMulai" name="tanggal_mulai" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Tanggal Selesai</label>
                        <input type="date" id="editSelesai" name="tanggal_selesai" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button class="btn btn-warning">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- Modal Rekap (PDF) --}}
<div class="modal fade" id="rekapModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Rekap Periode <span id="rekapNamaPeriode"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Placeholder tampilan PDF hasil generate. Nantinya src iframe diarahkan
                     ke route generate-pdf, contoh: /bendahara/periode/{id}/rekap/pdf --}}
                <div id="rekapLoading" class="text-center py-5">
                    <div class="spinner-border text-secondary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mb-0">Sedang men-generate PDF rekap...</p>
                </div>

                <iframe
                    id="rekapPdfViewer"
                    src=""
                    style="width:100%; height:70vh; border:1px solid #dee2e6; display:none;">
                </iframe>
            </div>
            <div class="modal-footer">
                <a href="#" id="rekapDownloadBtn" class="btn btn-primary" target="_blank">
                    <i class="bi bi-download"></i> Unduh PDF
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    // Alert otomatis hilang (tetap dipertahankan)
    const alert = document.querySelector('.alert');
    if(alert){
        setTimeout(function (){
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 3000);
    }

    // Script Modal Edit Periode (tetap dipertahankan)
    const editPeriodeModal = document.getElementById('editPeriodeModal');
    if(editPeriodeModal) {
        editPeriodeModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            document.getElementById('editNama').value = button.getAttribute('data-nama');
            document.getElementById('editMulai').value = button.getAttribute('data-mulai');
            document.getElementById('editSelesai').value = button.getAttribute('data-selesai');
            document.getElementById('editPeriodeForm').action = "{{ url('bendahara/periode') }}/" + id;
        });
    }

    // Script Modal Konfirmasi Aktifkan (tetap dipertahankan)
    const konfirmasiAktifkanModal = document.getElementById('konfirmasiAktifkanModal');
    if(konfirmasiAktifkanModal) {
        konfirmasiAktifkanModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('konfirmasiNamaPeriode').textContent = button.getAttribute('data-nama');
            document.getElementById('konfirmasiAktifkanForm').setAttribute('action', button.getAttribute('data-action'));
        });
    }

    // SCRIPT MODAL REKAP (Diperbarui)
    const rekapModal = document.getElementById('rekapModal');
    if(rekapModal) {
        rekapModal.addEventListener('show.bs.modal', function (event) {
            let button = event.relatedTarget;
            let id = button.getAttribute('data-id');
            let nama = button.getAttribute('data-nama');

            // Set judul modal
            document.getElementById('rekapNamaPeriode').textContent = nama;

            let loading = document.getElementById('rekapLoading');
            let viewer = document.getElementById('rekapPdfViewer');
            let downloadBtn = document.getElementById('rekapDownloadBtn');

            // Tampilkan loading, sembunyikan iframe lama
            loading.style.display = 'block';
            viewer.style.display = 'none';
            viewer.src = '';

            // Buat URL yang mengarah ke route DOMPDF
            let pdfUrl = `/bendahara/periode/${id}/rekap/pdf`;
            downloadBtn.setAttribute('href', pdfUrl);

            // Jika PDF sudah selesai dimuat oleh iframe, hilangkan loading
            viewer.onload = function() {
                loading.style.display = 'none';
                viewer.style.display = 'block';
            };

            // Tembak URL ke iframe untuk memulai proses generate dari Controller
            viewer.src = pdfUrl;
        });
    }
});
    </script>
@endsection