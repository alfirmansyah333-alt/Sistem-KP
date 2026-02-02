@extends('layouts.app')

@section('content')
    <p class="text-muted small mb-2">
        <span class="text-muted">Admin /</span> Dashboard
    </p>

    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-2">
                        <i class="bx bx-wave-right text-primary"></i> 
                        Selamat Datang, {{ Auth::user()->name }}!
                    </h4>
                    <p class="card-text text-muted mb-0">
                        Kelola seluruh data Kerja Praktek (KP) mahasiswa, dosen pembimbing, dan perusahaan mitra dari dashboard ini.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Card Mahasiswa -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 transition-all" style="transition: all 0.3s ease;">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="bg-label-primary p-3 rounded-circle">
                            <i class="bx bx-user fs-1 text-primary"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Total Mahasiswa</h5>
                    <h2 class="card-text text-primary mb-0" style="font-weight: 700;">{{ number_format($totalMahasiswa ?? 0) }}</h2>
                    <small class="text-muted">Peserta KP Aktif</small>
                </div>
            </div>
        </div>

        <!-- Card Dosen -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 transition-all" style="transition: all 0.3s ease;">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="bg-label-info p-3 rounded-circle">
                            <i class="bx bx-chalkboard fs-1 text-info"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Dosen Pembimbing</h5>
                    <h2 class="card-text text-info mb-0" style="font-weight: 700;">{{ number_format($totalDosen ?? 0) }}</h2>
                    <small class="text-muted">Pembimbing Akademik</small>
                </div>
            </div>
        </div>

        <!-- Card Perusahaan -->
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 transition-all" style="transition: all 0.3s ease;">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="bg-label-warning p-3 rounded-circle">
                            <i class="bx bx-buildings fs-1 text-warning"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Perusahaan Mitra</h5>
                    <h2 class="card-text text-warning mb-0" style="font-weight: 700;">{{ number_format($totalPerusahaan ?? 0) }}</h2>
                    <small class="text-muted">Tempat Kerja Praktek</small>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-label-primary { background-color: rgba(103, 109, 214, 0.1) !important; }
        .bg-label-info { background-color: rgba(23, 162, 184, 0.1) !important; }
        .bg-label-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
        .bg-label-success { background-color: rgba(40, 167, 69, 0.1) !important; }
        .card:hover { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
    </style>
@endsection