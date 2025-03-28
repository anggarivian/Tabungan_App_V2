@extends('layouts.main')

@section('content')
<div class="container text-center">
    <h1>Pembayaran Berhasil!</h1>
    <p>Tabungan Anda telah diperbarui.</p>
    <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Dashboard</a>
</div>
@endsection
