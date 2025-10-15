@extends('layout.main')

@section('title') Detail Buku - {{ $buku->tahun }} @endsection

@section('content')
<div class="page-heading mb-3">
  <div class="row align-items-center">
    <div class="col-12 col-md-6">
      <h3 class="mt-3">Detail Buku Tahun {{ $buku->tahun }}</h3>
    </div>
    <div class="col-12 col-md-6 text-md-end">
      <a href="{{ route('bendahara.pembukuan.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
    </div>
  </div>
</div>

<div class="page-content">
  <div class="accordion" id="accordionDetail">
    <div class="text-end mb-3">
      <a href="{{ route('bendahara.pembukuan.rekapTahunan', $buku->id) }}" target="_blank" class="btn btn-outline-danger">
        <i class="bi bi-file-earmark-pdf"></i> Download Rekap PDF
      </a>
    </div>

    {{-- Rombel --}}
    <div class="card mb-3">
      <div class="card-header bg-primary text-white" data-bs-toggle="collapse" data-bs-target="#collapseRombel">
        <h5 class="mb-0">Data Rombel ({{ $rombels->count() }})</h5>
      </div>
      <div id="collapseRombel" class="collapse show" data-bs-parent="#accordionDetail">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th>Nama Rombel</th>
                  <th class="text-center">Tingkat</th>
                  <th class="text-center">Wali Kelas</th>
                  <th class="text-center">Jumlah Siswa</th>
                </tr>
              </thead>
              <tbody>
                @forelse($rombels as $i => $r)
                <tr>
                  <td class="text-center">{{ $i + 1 }}</td>
                  <td>{{ $r->name }}</td>
                  <td class="text-center">{{ $r->kelas->name ?? '-' }}</td>
                  <td class="text-center">{{ $r->walikelas->name ?? '-' }}</td>
                  <td class="text-center">{{ $r->total_siswa }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Tidak ada rombel.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Walikelas --}}
    <div class="card mb-3">
      <div class="card-header bg-info text-white" data-bs-toggle="collapse" data-bs-target="#collapseWali">
        <h5 class="mb-0">Data Walikelas ({{ $walikelas->count() }})</h5>
      </div>
      <div id="collapseWali" class="collapse" data-bs-parent="#accordionDetail">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th>Nama</th>
                  <th>Username</th>
                  <th class="text-center">Kelas</th>
                  <th class="text-center">Rombel</th>
                  <th class="text-center">Kontak</th>
                </tr>
              </thead>
              <tbody>
                @forelse($walikelas as $i => $w)
                <tr>
                  <td class="text-center">{{ $i + 1 }}</td>
                  <td>{{ $w->name }}</td>
                  <td>{{ $w->username }}</td>
                  <td class="text-center">{{ $w->kelas->name ?? '-' }}</td>
                  <td class="text-center">{{ $w->rombel->name ?? '-' }}</td>
                  <td class="text-center">{{ $w->kontak ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada data walikelas.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Siswa --}}
    <div class="card">
      <div class="card-header bg-success text-white" data-bs-toggle="collapse" data-bs-target="#collapseSiswa">
        <h5 class="mb-0">Data Siswa ({{ $siswa->count() }})</h5>
      </div>
      <div id="collapseSiswa" class="collapse" data-bs-parent="#accordionDetail">
        <div class="card-body">
          <div class="mb-3 text-center">
            <div id="kelasFilter" class="btn-group" role="group" aria-label="Filter Kelas">
              <button data-kelas="" class="btn btn-sm btn-success active">Semua</button>
              @foreach($kelasList as $kelas)
                <button data-kelas="{{ $kelas->id }}" class="btn btn-sm btn-outline-success">
                  Kelas {{ $kelas->name }}
                </button>
              @endforeach
            </div>
          </div>
          <div class="table-responsive position-relative" id="siswaTableContainer">
            <div id="loadingOverlay" style="display:none; position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.8); z-index:10; text-align:center; padding-top:100px;">
              <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Memuat data siswa...</p>
            </div>

            <table class="table table-hover">
              <thead>
                <tr>
                  <th class="text-center">No</th>
                  <th>Nama</th>
                  <th class="text-center">Username</th>
                  <th class="text-center">Kelas</th>
                  <th class="text-center">Rombel</th>
                  <th class="text-center">Jenis Kelamin</th>
                  <th class="text-center">Orang Tua</th>
                  <th class="text-center">Kontak</th>
                </tr>
              </thead>
              <tbody id="siswaTableBody">
                @include('bendahara.pembukuan.partials._siswa_table_body', ['siswa' => $siswa])
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Tabungan --}}
    <div class="card mt-3">
      <div class="card-header bg-warning text-dark" data-bs-toggle="collapse" data-bs-target="#collapseTabungan">
        <h5 class="mb-0">Data Tabungan ({{ $tabungans->sum(fn($k) => $k->users->count()) }})</h5>
      </div>
      <div id="collapseTabungan" class="collapse" data-bs-parent="#accordionDetail">
        <div class="card-body">

          @forelse($tabungans as $kelas)
            <h6 class="fw-bold mt-2 mb-1">Kelas {{ $kelas->name }}</h6>
            <div class="table-responsive mb-3">
              <table class="table table-hover table-sm align-middle">
                <thead>
                  <tr class="table-light">
                    {{-- <th class="text-center" style="width:60px">No</th> --}}
                    <th style="width: 250px">Nama Siswa</th>
                    <th class="text-center">Saldo</th>
                    <th class="text-center">Premi</th>
                    <th class="text-center">Sisa</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($kelas->users as $i => $u)
                    @if($u->tabungan)
                      <tr>
                        {{-- <td class="text-center">{{ $i + 1 }}</td> --}}
                        <td style="width: 250px">{{ $u->name }}</td>
                        <td class="text-center">{{ number_format($u->tabungan->saldo, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($u->tabungan->premi, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($u->tabungan->sisa, 0, ',', '.') }}</td>
                      </tr>
                    @endif
                  @empty
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada siswa di kelas ini.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          @empty
            <p class="text-center text-muted">Belum ada data tabungan.</p>
          @endforelse

        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  // Optional: handle collapse icons
  document.querySelectorAll('.card-header[data-bs-toggle="collapse"]').forEach(header => {
    header.style.cursor = 'pointer'
    header.addEventListener('click', () => {
      const icon = header.querySelector('i')
      if (icon) icon.classList.toggle('bi-chevron-down')
    })
  })

  // ✅ Real-time siswa filter
  $(function() {
    $('#kelasFilter button').on('click', function() {
      const kelasId = $(this).data('kelas')
      const bukuId = {{ $buku->id }}
      const $btns = $('#kelasFilter button')
      const $tableBody = $('#siswaTableBody')
      const $loading = $('#loadingOverlay')

      // toggle active button state
      $btns.removeClass('btn-success active').addClass('btn-outline-success')
      $(this).addClass('btn-success active').removeClass('btn-outline-success')

      // show spinner
      $loading.show()

      // make AJAX request
      $.get(`{{ url('/bendahara/kelola-pembukuan') }}/${bukuId}/filter-siswa`, { kelas_id: kelasId })
        .done(function(res) {
          $tableBody.html(res.html)
        })
        .fail(function(xhr) {
          console.error(xhr.responseText)
          $tableBody.html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data</td></tr>')
        })
        .always(function() {
          $loading.hide()
        })
    })
  })
</script>
@endsection

