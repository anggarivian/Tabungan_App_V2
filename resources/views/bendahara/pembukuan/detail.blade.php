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
          <div class="table-responsive">
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
              <tbody>
                @forelse($siswa as $i => $s)
                <tr>
                  <td class="text-center">{{ $i + 1 }}</td>
                  <td>{{ $s->name }}</td>
                  <td class="text-center">{{ $s->username }}</td>
                  <td class="text-center">{{ $s->kelas->name ?? '-' }}</td>
                  <td class="text-center">{{ $s->rombel->name ?? '-' }}</td>
                  <td class="text-center">{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                  <td class="text-center">{{ $s->orang_tua ?? '-' }}</td>
                  <td class="text-center">{{ $s->kontak ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center">Tidak ada siswa untuk buku ini.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection