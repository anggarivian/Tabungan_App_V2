@extends('layout.main')

@section('content')
    <style>
        /* Reset some potential conflicting styles from main layout */
        .success-container * {
            box-sizing: border-box;
        }

        /* Tambahan style untuk ilustrasi */
        .success-illustration {
            margin-bottom: 20px;
            animation: fadeIn 0.8s ease-in-out;
        }

        .success-container {
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            text-align: center;
            padding: 40px 20px;
            animation: fadeIn 0.6s ease-in-out;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background-color: #4ade80;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 25px;
            animation: scaleIn 0.5s ease-out;
        }

        .success-title {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 10px;
        }

        .success-message {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 30px;
        }

        .success-details {
            background-color: #ffffff;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: left;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 15px;
            color: #334155;
        }

        .detail-label {
            font-weight: 500;
        }

        .btn {
            display: inline-block;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            color: #64748b;
            border: 1px solid #cbd5e1;
            margin-right: 10px;
        }

        .btn-outline:hover {
            background-color: #f1f5f9;
        }

        .button-group {
            margin-top: 20px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            70% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .transaction-id {
            font-size: 14px;
            color: #94a3b8;
            margin-top: 25px;
        }

        .checkmark {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 3;
            stroke-miterlimit: 10;
            stroke: white;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }
    </style>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 30vh;">
    <div class="success-container">
        <img src="Banknote-rafiki.svg" height="250px" alt="">

        <h1 class="success-title">Pembayaran Berhasil!</h1>

        <div class="success-details">
            <div class="detail-item">
                <span class="detail-label">Tanggal</span>
                <span id="current-date">-</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Metode Pembayaran</span>
                <span>Transfer Bank</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Status</span>
                <span style="color: #22c55e; font-weight: 600;">Sukses</span>
            </div>
        </div>

        <p class="success-message">Terima kasih, pembayaran Anda telah berhasil diproses</p>

        <div class="button-group">
            <a href="{{ route(Auth::user()->roles_id == 1 ? 'kepsek.dashboard' : (Auth::user()->roles_id == 2 ? 'bendahara.dashboard' : (Auth::user()->roles_id == 3 ? 'walikelas.dashboard' : 'siswa.dashboard'))) }}" class="btn btn-primary">Kembali ke Dashboard</a>
        </div>

        <div class="transaction-id">
            ID Transaksi: <span id="transaction-id">TRX-20250509-10527</span>
        </div>
    </div>
@endsection
