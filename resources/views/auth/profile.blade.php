@extends('layout.main')

@section('title', 'Profil Saya - SakuRame')

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Profil Saya</h3>
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
                    <li class="breadcrumb-item active">Profil</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form id="profilForm" action="{{ route('profil.update') }}" method="POST" class="card shadow-lg p-4">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label>Nama</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                    class="form-control input-field" placeholder="Masukkan nama lengkap">
            </div>

            <div class="col-md-6">
                <label>Email Aktif</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                    class="form-control input-field" placeholder="Masukkan email aktif">
                <small class="text-muted">Email digunakan untuk notifikasi tabungan.</small>
            </div>

            <div class="col-md-6">
                <label>Nomor WhatsApp Aktif</label>
                <input type="text" name="kontak" value="{{ old('kontak', auth()->user()->kontak) }}"
                    class="form-control input-field" placeholder="08xxxxxxxxxx">
                <small class="text-muted">Notifikasi tabungan akan dikirim ke nomor ini.</small>
            </div>

            <div class="col-md-6">
                <label>Alamat</label>
                <input type="text" name="alamat" value="{{ old('alamat', auth()->user()->alamat) }}"
                    class="form-control input-field" placeholder="Masukkan alamat">
            </div>

            <div class="col-md-6">
                <label>Nama Orang Tua / Wali</label>
                <input type="text" name="orang_tua" value="{{ old('orang_tua', auth()->user()->orang_tua) }}"
                    class="form-control input-field" placeholder="Masukkan nama orang tua/wali">
            </div>
        </div>

        <hr class="my-4">

        <h6 class="fw-bold">Ubah Password</h6>
        <small class="text-muted">Isi password lama untuk mengatur password baru.</small>

        <div class="row g-3 mt-2">
            <div class="col-md-12">
                <label>Password Lama</label>
                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Password lama">
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label>Password Baru</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password baru">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Ulangi password baru">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <button type="submit" id="saveButton" class="btn btn-primary w-100 mt-4" disabled>Simpan Perubahan</button>
    </form>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('profilForm');
        const inputs = form.querySelectorAll('.input-field');
        const saveButton = document.getElementById('saveButton');

        const initial = {};
        inputs.forEach(i => initial[i.name] = i.value);

        const checkChanges = () => {
            let changed = false;
            inputs.forEach(i => {
                if (i.value !== initial[i.name]) changed = true;
            });
            const hasPassword = form.current_password.value.trim() !== "" ||
                                form.password.value.trim() !== "" ||
                                form.password_confirmation.value.trim() !== "";
            saveButton.disabled = !(changed || hasPassword);
        }

        inputs.forEach(i => i.addEventListener('input', checkChanges));
        form.current_password.addEventListener('input', checkChanges);
        form.password.addEventListener('input', checkChanges);
        form.password_confirmation.addEventListener('input', checkChanges);
    });
</script>
@endsection
