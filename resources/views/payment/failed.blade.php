@extends('layouts.main')

@section('content')
<div class="container text-center">
    <h1>Pembayaran Gagal!</h1>
    <p>Silakan coba lagi atau hubungi admin.</p>
    <a href="{{ route('home') }}" class="btn btn-danger">Kembali ke Dashboard</a>
</div>
@endsection
