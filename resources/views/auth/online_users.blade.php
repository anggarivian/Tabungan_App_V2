@extends('layout.main')

@section('title', 'Online User - SakuRame')

@section('content')
<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <h3 class="mt-3">Online User</h3>
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
    <div class="card shadow-lg">
        <div class="card-body">
            <h2>Status User</h2>

            {{-- PerPage & Pagination Controls --}}
            <div class="d-flex justify-content-between">
                <form method="GET" action="{{ request()->url() }}">
                    <div class="d-flex align-items-center mb-3">
                        <label for="perPage" class="me-2">Show</label>
                        <select name="perPage" id="perPage"
                                class="form-select form-select-sm"
                                onchange="this.form.submit()">
                            @foreach([10,25,50,75,100] as $n)
                                <option value="{{ $n }}" @if(request('perPage',10)==$n) selected @endif>
                                    {{ $n }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- Tabs --}}
            <ul class="nav nav-tabs" id="userTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="online-tab"
                            data-bs-toggle="tab" data-bs-target="#online"
                            type="button" role="tab">User Online</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="weak-tab"
                            data-bs-toggle="tab" data-bs-target="#weak"
                            type="button" role="tab">Sudah Ganti Password</button>
                </li>
            </ul>

            <div class="tab-content mt-3" id="userTabContent">
                {{-- Online Users --}}
                <div class="tab-pane fade show active" id="online" role="tabpanel">
                    @if($onlineUsers->count())
                        <table class="table table-bordered">
                            <thead>
                                <tr><th>Nama</th><th>Email</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach($onlineUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge bg-success">Online</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-end">
                            {{ $onlineUsers
                                ->appends(['perPage'=>request('perPage')])
                                ->links('layout.pagination.bootstrap-5') }}
                        </div>
                    @else
                        <div class="alert alert-secondary">Tidak ada user yang sedang online.</div>
                    @endif
                </div>

                {{-- Weak Password Users --}}
                <div class="tab-pane fade" id="weak" role="tabpanel">
                    @if($notWeakPasswordUsers->count())
                        <table class="table table-bordered">
                            <thead>
                                <tr><th>Nama</th><th>Email</th><th>Status Password</th></tr>
                            </thead>
                            <tbody>
                                @foreach($notWeakPasswordUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge bg-success">Done</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-end">
                            {{ $notWeakPasswordUsers
                                ->appends(['perPage'=>request('perPage')])
                                ->links('layout.pagination.bootstrap-5') }}
                        </div>
                    @else
                        <div class="alert alert-info">Tidak ada user yang menggunakan password 12345.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection
