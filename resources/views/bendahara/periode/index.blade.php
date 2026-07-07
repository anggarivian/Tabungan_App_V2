@extends('layout.main')

@section('title') Periode - SakuRame @endsection

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Periode</h3>
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
                    <li class="breadcrumb-item active" aria-current="page">Periode</li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<div class="page-content">
    <div class="card shadow">
        <div class="card-body pb-1 pt-3">
            <div class="card-body pb-1 pt-3">

                <div class="mb-3">
                    <button
                        class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#tambahPeriodeModal">

                        <i class="bi bi-plus-circle"></i>

                        Tambah Periode

                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" width="60">No</th>
                                <th>Periode</th>
                                <th class="text-center">
                                    Dari Tanggal
                                </th>
                                <th class="text-center">
                                    Sampai Tanggal
                                </th>
                                <th class="text-center">
                                    Status
                                </th>
                                <th class="text-center" width="340">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($periodes as $periode)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $periode->nama }}

                                @if($periode->status)
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            </td>

                            <td>
                                {{ $periode->tanggal_mulai }}
                                s/d
                                {{ $periode->tanggal_selesai }}
                            </td>

                            <td>

                                @if(!$periode->status)

                                    <form action="{{ route('bendahara.periode.aktifkan',$periode->id) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf
                                        @method('PUT')

                                        <button class="btn btn-success btn-sm">
                                            Aktifkan
                                        </button>

                                    </form>

                                @endif

                                <button class="btn btn-warning btn-sm">
                                    Edit
                                </button>

                                <button class="btn btn-info btn-sm">
                                    Lihat Data
                                </button>

                                <button class="btn btn-secondary btn-sm">
                                    Rekap
                                </button>

                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <form
                        method="GET"
                        action="{{ request()->url() }}">
                        <div class="d-flex">
                            <label
                                class="me-2 mt-1">
                                Show
                            </label>
                            <select
                                name="perPage"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()">
                                <option
                                    value="10"
                                    {{ request('perPage') == 10 ? 'selected' : '' }}>
                                    10
                                </option>
                                <option
                                    value="25"
                                    {{ request('perPage') == 25 ? 'selected' : '' }}>
                                    25
                                </option>
                                <option
                                    value="50"
                                    {{ request('perPage') == 50 ? 'selected' : '' }}>
                                    50
                                </option>
                                <option
                                    value="100"
                                    {{ request('perPage') == 100 ? 'selected' : '' }}>
                                    100
                                </option>
                            </select>
                        </div>
                    </form>
                    <div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div
class="modal fade"
id="tambahPeriodeModal">

<div class="modal-dialog">

<div class="modal-content">

<form
method="POST"
action="{{ route('periode.store') }}">

@csrf

<div class="modal-header">

<h5>

Tambah Periode

</h5>

<button
class="btn-close"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<div class="mb-3">

<label>

Periode

</label>

<input
type="text"
name="nama"
class="form-control"
placeholder="2026 / 2027">

</div>

<div class="mb-3">

<label>

Tanggal Mulai

</label>

<input
type="date"
name="tanggal_mulai"
class="form-control">

</div>

<div class="mb-3">

<label>

Tanggal Selesai

</label>

<input
type="date"
name="tanggal_selesai"
class="form-control">

</div>

</div>

<div class="modal-footer">

<button
class="btn btn-secondary"
data-bs-dismiss="modal">

Batal

</button>

<button
class="btn btn-primary">

Simpan

</button>

</div>

</form>

</div>

</div>

</div>

@endsection

@section('js')
    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tambahPeriodeModal = new bootstrap.Modal(document.getElementById('tambahPeriodeModal'));
            tambahPeriodeModal.show();
        });
    </script>
    @endif
@endsection