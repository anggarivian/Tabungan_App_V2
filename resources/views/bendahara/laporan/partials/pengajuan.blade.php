<div class="mt-3">
    <form id="exportPengajuanForm" action="{{ route('export.pengajuan') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <label for="siswaPengajuan" class="form-label">Pilih Siswa</label>
                <select class="form-select" id="siswaPengajuan" name="siswa_id">
                    <option value="">Semua Siswa</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="kelasPengajuan" class="form-label">Pilih Kelas</label>
                <select class="form-select" id="kelasPengajuan" name="kelas_id">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $kelasItem)
                        <option value="{{ $kelasItem->id }}">{{ $kelasItem->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="startDatePengajuan" class="form-label">Tanggal Awal</label>
                <input type="date" class="form-control" id="startDatePengajuan" name="start_date">
            </div>
            <div class="col-md-3">
                <label for="endDatePengajuan" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="endDatePengajuan" name="end_date">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary" name="export_type" value="pdf">Export PDF</button>
                <button type="submit" class="btn btn-success" name="export_type" value="excel">Export Excel</button>
            </div>
        </div>
    </form>
</div>
