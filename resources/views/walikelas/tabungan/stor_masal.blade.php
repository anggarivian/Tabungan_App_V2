@extends('layout.main')

@section('title') Stor Tabungan - SakuRame @endsection

@section('content')
<div class="page-heading mb-2">
    <div class="d-flex justify-content-between">
        <h3 class="mt-3">Stor Tabungan</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-right">
                @if(auth()->user()->roles_id == 1)
                    <li class="breadcrumb-item"><a href="{{ route ('kepsek.dashboard')}}">Dashboard</a></li>
                @elseif(auth()->user()->roles_id == 2)
                    <li class="breadcrumb-item"><a href="{{ route ('bendahara.dashboard')}}">Dashboard</a></li>
                @elseif(auth()->user()->roles_id == 3)
                    <li class="breadcrumb-item"><a href="{{ route ('walikelas.dashboard')}}">Bendahara</a></li>
                @elseif(auth()->user()->roles_id == 4)
                    <li class="breadcrumb-item"><a href="{{ route ('siswa.dashboard')}}">Siswa</a></li>
                @endif
                <li class="breadcrumb-item"><a href="{{ route ('walikelas.tabungan.index')}}">Tabungan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Stor</li>
            </ol>
        </nav>
    </div>
</div>
<div class="page-content">
    <div class="card pd-0">
        <div class="card-body">
            <div class="row">
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
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <tr>
                            <td><h6>Kelas</h6></td>
                            <td ><h6>{{$kelas->name}}</h6></td>
                            <td><h6>Jumlah Siswa</h6></td>
                            <td ><h6>{{count($siswa)}}</h6></td>
                        </tr>
                        <tr>
                            <td><h6>Walikelas</h6></td>
                            <td ><h6>{{$walikelas->name ?? '-'}}</h6></td>
                            <td><h6>Jumlah Stor Kemarin</h6></td>
                            <td ><h6>Rp. {{ number_format($jumlahTransaksiKemarin)}}</h6></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="post" action="{{ route('walikelas.tabungan.storMasalTabungan') }}" enctype="multipart/form-data">
                @csrf
                <div class="table-responsive-lg">
                    <table id="table-data" class="table table-stripped">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Nama</th>
                                <th>Saldo Awal</th>
                                <th>Kode</th>
                                <th>Stor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no=1; $no1=0; $no2=0; $no3=0; $no4=0; $no5=0; $no6=0; $no7=0; $no8=0; $no9=0; @endphp
                            @foreach($siswa as $siswas)
                                <tr>
                                    <td class="text-center col-md-1">{{$no++}}</td>
                                    <td class="text-center col-md-3">
                                        <input type="text" class="form-control" name="input[{{$no2++}}][nama]" value="{{$siswas->name}}" style="border: none; outline: none; background: transparent;" readonly tabindex="-1" />
                                    </td>
                                    <td class="text-center col-md-3">
                                        <input type="text" class="form-control text-center" name="input[{{$no3++}}][saldo]" value="{{$siswas->tabungan->saldo ?? 0}}" style="border: none; outline: none; background: transparent;" readonly tabindex="-1"/>
                                    </td>
                                    <td class="text-center col-md-2">
                                        <input type="text" class="form-control text-center" name="input[{{$no1++}}][username]" value="{{$siswas->username}}" style="border: none; outline: none; background: transparent;" readonly tabindex="-1"/>
                                    </td>
                                    <td class="text-center col-md-3 col-9">
                                        <input type="text" class="form-control text-center" name="input[{{$no4++}}][stor]" id="stor_{{$no4}}" oninput="updateCalculation(this)" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end ">
                                    <strong>Jumlah Stor: </strong><span id="total-stor">0</span><br>
                                    <strong>Jumlah Nabung: </strong><span id="filled-count">0</span> dari <span id="total-count">{{ count($siswa) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Stor Tabungan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();

            var username = $('#username').val();

            $.ajax({
                url: "{{ route('walikelas.search') }}",
                method: "GET",
                data: { username: username },
                success: function(data) {
                    if (data) {
                        console.log(data);
                        if(data.name !== 'Tidak Ada' && data.kelas !== 'Tidak Ada' && data.tabungan !== 'Tidak Ada') {
                            $('#name').val(data.name);
                            $('#kelas').val(data.kelas);
                            $('#jumlah_tabungan').val(data.tabungan);
                        } else {
                            $('#tidak-ada').html('<div class="alert alert-danger">Data tidak ditemukan</div>');
                            setTimeout(function() {
                                $('#tidak-ada').empty();
                            }, 2000);
                            $('#name').val('');
                            $('#kelas').val('');
                            $('#jumlah_tabungan').val('');
                        }
                        $('#jumlah_stor').focus();
                    } else {
                        alert('User tidak ditemukan');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan: ' + error);
                }
            });
        });

        $('#username').on('keypress', function(e) {
            if(e.which === 13) {
                $('#jumlah_stor').focus();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            const firstStorInput = document.querySelector('input[name^="input"][name$="[stor]"]');
            if (firstStorInput) {
                firstStorInput.focus();
            }
        }, 1000); // Delay 100ms untuk memastikan elemen sepenuhnya dimuat
    });

    // Fungsi untuk menghapus pemisah ribuan dan mengembalikan angka
    function removeThousandsSeparator(value) {
        return value.replace(/[^0-9.-]+/g, ''); // Menghapus semua karakter selain angka dan tanda minus
    }

    // Fungsi untuk memperbarui perhitungan
    function updateCalculation(inputElement) {
        let totalStor = 0;
        let filledCount = 0;
        const numberFormat = new Intl.NumberFormat(); // Format angka dengan pemisah ribuan

        // Ambil semua input dengan id yang diawali 'stor_'
        document.querySelectorAll('input[id^="stor_"]').forEach(function(input) {
            let value = removeThousandsSeparator(input.value); // Menghapus pemisah ribuan
            value = parseFloat(value) || 0; // Jika tidak ada angka, anggap 0
            if (value > 0) {
                filledCount++; // Hitung input yang diisi
            }
            totalStor += value;
        });

        // Update tampilan total stor dengan format angka
        document.getElementById('total-stor').textContent = numberFormat.format(totalStor);

        // Update jumlah data yang diisi
        document.getElementById('filled-count').textContent = filledCount;

        // Setelah input selesai, tambahkan pemisah ribuan
        inputElement.value = numberFormat.format(removeThousandsSeparator(inputElement.value));
    }

</script>


@endsection
