@extends('layout.main')

@section('title', 'Export Data - SakuRame')

@section('content')

<style>
    .page-heading h3 {
        font-size: 1.6rem;
        background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: gradientMove 3s ease-in-out infinite;
        font-weight: 800;
    }

    @keyframes gradientMove {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .nav-tabs-custom .nav-link {
        color: #6c757d;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        background: transparent;
    }

    .nav-tabs-custom .nav-link.active {
        color: #764ba2;
        border-bottom-color: #764ba2;
        background: transparent;
    }

    .searchable-dropdown { position: relative; }

    .searchable-dropdown .dropdown-input {
        cursor: pointer;
        background: var(--bs-body-bg, #fff);
    }

    .searchable-dropdown .dropdown-input.open-border {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        box-shadow: none;
    }

    .searchable-dropdown .dropdown-list-wrapper {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        z-index: 1050;
        border-radius: 0 0 .5rem .5rem;
        overflow: hidden;
        background: var(--bs-body-bg, #fff);
        border: 1px solid var(--bs-border-color, #dee2e6);
        box-shadow: 0 10px 25px rgba(0,0,0,.08);
    }

    .searchable-dropdown.open .dropdown-list-wrapper { display: block; }

    .searchable-dropdown .search-input {
        width: 100%;
        border: 0;
        outline: none;
        padding: .75rem;
        background: var(--bs-body-bg, #fff);
        color: var(--bs-body-color, #212529);
        border-bottom: 1px solid var(--bs-border-color, #dee2e6);
    }

    .searchable-dropdown .search-input::placeholder { color: var(--bs-secondary-color, #6c757d); }

    .searchable-dropdown .dropdown-items {
        max-height: 250px;
        overflow-y: auto;
        background: var(--bs-body-bg, #fff);
    }

    .searchable-dropdown .dropdown-item-option {
        padding: .7rem .85rem;
        cursor: pointer;
        transition: .15s;
        color: var(--bs-body-color, #212529);
        border-bottom: 1px solid var(--bs-border-color-translucent, rgba(0,0,0,.08));
    }

    .searchable-dropdown .dropdown-item-option:hover { background: var(--bs-secondary-bg, #f8f9fa); }

    .searchable-dropdown .dropdown-item-option.selected {
        background: rgba(var(--bs-primary-rgb), .15);
        color: var(--bs-primary);
        font-weight: 600;
    }

    .searchable-dropdown .no-results {
        padding: .75rem;
        color: var(--bs-secondary-color, #6c757d);
        background: var(--bs-body-bg, #fff);
    }
</style>

<div class="page-heading mb-3">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h3>Export Data Keuangan</h3>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card shadow-sm">
        <div class="card-body p-0">
            {{-- fix: tab Pengajuan dihapus, walikelas tidak menangani pengajuan penarikan --}}
            <ul class="nav nav-tabs nav-tabs-custom px-4 pt-2 border-bottom" id="exportTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="tab-tbg" data-bs-toggle="tab" data-bs-target="#pane-tbg" type="button">
                        <i class="bi bi-wallet2 me-2"></i>Tabungan
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="tab-trx" data-bs-toggle="tab" data-bs-target="#pane-trx" type="button">
                        <i class="bi bi-arrow-left-right me-2"></i>Transaksi
                    </button>
                </li>
            </ul>

        <div class="tab-content p-4">
            @php
                $forms = [
                    'tbg' => 'walikelas.export.tabungan',
                    'trx' => 'walikelas.export.transaksi',
                ];
            @endphp

            @if($rombels->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Anda belum memiliki rombel pada periode yang dipilih. Hubungi bendahara untuk ditugaskan sebagai wali kelas.
                </div>
            @endif

            @foreach($forms as $key => $route)
            <div class="tab-pane fade @if($loop->first) show active @endif" id="pane-{{ $key }}" role="tabpanel">
                <form action="{{ route($route) }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Cakupan Data</label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline me-3">
                                    <input class="form-check-input radio-option" type="radio"
                                           name="export_option" id="opt-siswa-{{ $key }}"
                                           value="siswa" checked data-key="{{ $key }}">
                                    <label class="form-check-label" for="opt-siswa-{{ $key }}">Per Siswa</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input radio-option" type="radio"
                                           name="export_option" id="opt-semua-{{ $key }}"
                                           value="semua" data-key="{{ $key }}">
                                    {{-- fix: label diperjelas -- ini scope-nya rombel milik walikelas, bukan seluruh sekolah --}}
                                    <label class="form-check-label" for="opt-semua-{{ $key }}">Semua Siswa di Rombel Saya</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3" id="section-siswa-{{ $key }}">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pilih Siswa</label>
                            <input type="hidden" name="siswa_id" id="siswaId-{{ $key }}">

                            <div class="searchable-dropdown" id="siswaDropdown-{{ $key }}">
                                <input type="text" class="form-control dropdown-input"
                                       id="siswaDisplay-{{ $key }}"
                                       placeholder="Cari atau pilih siswa..." readonly>

                                <div class="dropdown-list-wrapper">
                                    <input type="text" class="search-input" placeholder="Ketik untuk mencari...">
                                    <div class="dropdown-items">
                                        <div class="dropdown-item-option" data-value="">-- Pilih Siswa --</div>
                                        @foreach($siswas as $siswa)
                                            <div class="dropdown-item-option" data-value="{{ $siswa->id }}">
                                                {{ $siswa->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Rombel</label>
                            <select class="form-select" name="rombel_id">
                                <option value="">Semua Rombel Saya</option>
                                @foreach($rombels as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pilih Periode</label>
                            <select class="form-select" name="periode_id">
                                @forelse($periodes as $p)
                                    <option value="{{ $p->id }}" {{ \App\Helpers\PeriodeHelper::id() == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @empty
                                    <option value="">Tidak ada periode tersedia</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-3 d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-sm btn-danger px-3" name="export_type" value="pdf" {{ $rombels->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                        </button>
                        <button type="submit" class="btn btn-sm btn-success px-3" name="export_type" value="excel" {{ $rombels->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>

</div>
@endsection

@section('js')
<script>
function initSearchableDropdown(wrapperId, hiddenId, displayId) {
    const wrapper = document.getElementById(wrapperId);
    if (!wrapper) return;

    const hiddenInput = document.getElementById(hiddenId);
    const displayInput = document.getElementById(displayId);
    const searchInput = wrapper.querySelector('.search-input');
    const itemsContainer = wrapper.querySelector('.dropdown-items');

    function openDropdown() {
        closeAllDropdowns();
        wrapper.classList.add('open');
        displayInput.classList.add('open-border');
        searchInput.value = '';
        filterItems('');
        setTimeout(() => searchInput.focus(), 0);
    }

    function selectItem(item) {
        const value = item.dataset.value;
        const text = item.textContent.trim();

        hiddenInput.value = value;
        displayInput.value = value ? text : '';

        itemsContainer.querySelectorAll('.dropdown-item-option').forEach(el => el.classList.remove('selected'));
        item.classList.add('selected');

        closeAllDropdowns();
    }

    function filterItems(query) {
        const items = itemsContainer.querySelectorAll('.dropdown-item-option');
        let hasResult = false;

        items.forEach(item => {
            const text = item.textContent.toLowerCase();

            if (!item.dataset.value) {
                item.style.display = 'block';
                return;
            }

            if (text.includes(query)) {
                item.style.display = 'block';
                hasResult = true;
            } else {
                item.style.display = 'none';
            }
        });

        let noResult = itemsContainer.querySelector('.no-results');

        if (!hasResult && query) {
            if (!noResult) {
                noResult = document.createElement('div');
                noResult.className = 'no-results';
                noResult.textContent = 'Siswa tidak ditemukan';
                itemsContainer.appendChild(noResult);
            }
            noResult.style.display = 'block';
        } else if (noResult) {
            noResult.style.display = 'none';
        }
    }

    displayInput.addEventListener('click', function (e) {
        e.stopPropagation();
        const isOpen = wrapper.classList.contains('open');
        isOpen ? closeAllDropdowns() : openDropdown();
    });

    searchInput.addEventListener('input', function () {
        filterItems(this.value.toLowerCase());
    });

    itemsContainer.addEventListener('click', function (e) {
        const item = e.target.closest('.dropdown-item-option');
        if (!item) return;
        selectItem(item);
    });

    document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target)) {
            wrapper.classList.remove('open');
            displayInput.classList.remove('open-border');
        }
    });
}

function closeAllDropdowns() {
    document.querySelectorAll('.searchable-dropdown.open').forEach(d => {
        d.classList.remove('open');
        const display = d.querySelector('.dropdown-input');
        if (display) display.classList.remove('open-border');
    });
}

document.addEventListener('DOMContentLoaded', function () {
    @foreach($forms as $key => $route)
        initSearchableDropdown('siswaDropdown-{{ $key }}', 'siswaId-{{ $key }}', 'siswaDisplay-{{ $key }}');
    @endforeach

    document.querySelectorAll('.radio-option').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const key = this.dataset.key;
            const section = document.getElementById(`section-siswa-${key}`);
            const hidden = document.getElementById(`siswaId-${key}`);
            const display = document.getElementById(`siswaDisplay-${key}`);
            const wrapper = document.getElementById(`siswaDropdown-${key}`);

            if (this.value === 'siswa') {
                section.style.display = '';
            } else {
                section.style.display = 'none';
                if (hidden) hidden.value = '';
                if (display) display.value = '';
                if (wrapper) wrapper.querySelectorAll('.dropdown-item-option').forEach(el => el.classList.remove('selected'));
                closeAllDropdowns();
            }
        });
    });
});
</script>
@endsection