@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-2">
                        <i class="bx bx-wave-right text-primary"></i>
                        Selamat Datang, {{ auth()->user()->name }}
                    </h4>
                    <p class="card-text text-muted mb-0">
                        Pantau ringkasan mahasiswa bimbingan dan laporan KP dari dashboard dosen.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Mahasiswa Bimbingan -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-label-primary rounded-circle mb-3" style="width: 80px; height: 80px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                        <i class="bx bx-group" style="font-size: 40px; color: #285AEB;"></i>
                    </div>
                    <h6 class="text-muted mb-1">Total Mahasiswa Bimbingan</h6>
                    <h3 class="mb-0">{{ $totalMahasiswaBimbingan }}</h3>
                    <small class="text-muted">Mahasiswa yang Anda bimbing</small>
                </div>
            </div>
        </div>

        <!-- Laporan Menunggu Persetujuan -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-label-warning rounded-circle mb-3" style="width: 80px; height: 80px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                        <i class="bx bx-time" style="font-size: 40px; color: #FF9F43;"></i>
                    </div>
                    <h6 class="text-muted mb-1">Laporan Menunggu</h6>
                    <h3 class="mb-0">{{ $laporanMenungguPersetujuan }}</h3>
                    <small class="text-muted">Laporan yang perlu disetujui</small>
                </div>
            </div>
        </div>

        <!-- Total Laporan Disetujui -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-label-success rounded-circle mb-3" style="width: 80px; height: 80px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                        <i class="bx bx-check-circle" style="font-size: 40px; color: #71DD37;"></i>
                    </div>
                    <h6 class="text-muted mb-1">Laporan Disetujui</h6>
                    <h3 class="mb-0">{{ $laporanDisetujui }}</h3>
                    <small class="text-muted">Total laporan yang disetujui</small>
                </div>
            </div>
        </div>

        <!-- Laporan Belum Bernilai -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg bg-label-danger rounded-circle mb-3" style="width: 80px; height: 80px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                        <i class="bx bx-star" style="font-size: 40px; color: #FF5733;"></i>
                    </div>
                    <h6 class="text-muted mb-1">Belum Diberi Nilai</h6>
                    <h3 class="mb-0">{{ $laporanBelumBernilai }}</h3>
                    <small class="text-muted">Laporan yang perlu dinilai</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
