<div class="mt-3">
    <form id="exportTransaksiForm" action="{{ route('export.transaksi') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-3">
                <label for="siswaTransaksi" class="form-label">Pilih Siswa</label>
                <select class="form-select" id="siswaTransaksi" name="siswa_id">
                    <option value="">Semua Siswa</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="kelasTransaksi" class="form-label">Pilih Kelas</label>
                <select class="form-select" id="kelasTransaksi" name="kelas_id">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $kelasItem)
                        <option value="{{ $kelasItem->id }}">{{ $kelasItem->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="startDateTransaksi" class="form-label">Tanggal Awal</label>
                <input type="date" class="form-control" id="startDateTransaksi" name="start_date">
            </div>
            <div class="col-md-3">
                <label for="endDateTransaksi" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="endDateTransaksi" name="end_date">
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
