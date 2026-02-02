@extends('layouts.app')

@section('content')
    <p class="text-muted small mb-2">
        <span class="text-muted">Koordinator /</span> Dashboard
    </p>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-2">
                        <i class="bx bx-wave-right text-primary"></i>
                        Selamat Datang, {{ Auth::user()->name }}
                    </h4>
                    <p class="card-text text-muted mb-0">
                        Pantau ringkasan pengajuan, penerimaan, dan seminar KP mahasiswa dari dashboard koordinator.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="bg-label-success p-3 rounded-circle">
                            <i class="bx bx-check-circle fs-1 text-success"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Pengajuan Diterima</h5>
                    <h2 class="card-text text-success mb-0" style="font-weight: 700;">{{ number_format($pengajuanMenunggu ?? 0) }}</h2>
                    <small class="text-muted">Siap untuk penerimaan</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="bg-label-secondary p-3 rounded-circle">
                            <i class="bx bx-layer fs-1 text-secondary"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Total Pengajuan</h5>
                    <h2 class="card-text text-secondary mb-0" style="font-weight: 700;">{{ number_format($totalPengajuan ?? 0) }}</h2>
                    <small class="text-muted">Semua pengajuan KP</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="bg-label-info p-3 rounded-circle">
                            <i class="bx bx-file fs-1 text-info"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Penerimaan Menunggu</h5>
                    <h2 class="card-text text-info mb-0" style="font-weight: 700;">{{ number_format($penerimaanMenunggu ?? 0) }}</h2>
                    <small class="text-muted">Perlu verifikasi koordinator</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="bg-label-success p-3 rounded-circle">
                            <i class="bx bx-calendar fs-1 text-success"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Seminar Menunggu</h5>
                    <h2 class="card-text text-success mb-0" style="font-weight: 700;">{{ number_format($seminarMenunggu ?? 0) }}</h2>
                    <small class="text-muted">Menunggu persetujuan</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="bg-label-primary p-3 rounded-circle">
                            <i class="bx bx-group fs-1 text-primary"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Mahasiswa Binaan</h5>
                    <h2 class="card-text text-primary mb-0" style="font-weight: 700;">{{ number_format($totalMahasiswa ?? 0) }}</h2>
                    <small class="text-muted">Total binaan koordinator</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="bg-label-danger p-3 rounded-circle">
                            <i class="bx bx-user-x fs-1 text-danger"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Belum Ada Pembimbing</h5>
                    <h2 class="card-text text-danger mb-0" style="font-weight: 700;">{{ number_format($mahasiswaBelumDosen ?? 0) }}</h2>
                    <small class="text-muted">Perlu assign dosen</small>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-label-primary { background-color: rgba(103, 109, 214, 0.1) !important; }
        .bg-label-info { background-color: rgba(23, 162, 184, 0.1) !important; }
        .bg-label-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
        .bg-label-success { background-color: rgba(40, 167, 69, 0.1) !important; }
        .bg-label-secondary { background-color: rgba(108, 117, 125, 0.1) !important; }
        .bg-label-danger { background-color: rgba(220, 53, 69, 0.1) !important; }
    </style>
@endsection
