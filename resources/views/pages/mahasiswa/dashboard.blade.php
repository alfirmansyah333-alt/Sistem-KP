@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
@php
  $user = auth()->user();
  $pengajuanTerbaru = $user?->pengajuanKP()->latest()->first();
  $pengajuanDiterima = $user?->pengajuanKP()->where('status', 'diterima')->latest()->first();
  $penerimaanTerbaru = $user?->penerimaanKP()->latest()->first();
  $penerimaanDiterima = $user?->penerimaanKP()->where('status', 'diterima')->latest()->first();
  $laporanTerbaru = $user?->laporanKP()->latest()->first();
  $laporanDisetujui = $user?->laporanKP()->where('status_approve', 'disetujui')->latest()->first();

  // Prioritaskan status yang diterima/disetujui
  $statusPengajuan = $pengajuanDiterima?->status ?? $pengajuanTerbaru?->status ?? null;
  $statusPenerimaan = $penerimaanDiterima?->status ?? $penerimaanTerbaru?->status ?? null;
  $statusLaporan = $laporanDisetujui?->status_approve ?? $laporanTerbaru?->status_approve ?? null;

  $step = 'Belum ada pengajuan KP. Silakan ajukan KP terlebih dahulu.';
  if ($pengajuanDiterima && !$penerimaanDiterima) {
      $step = 'Pengajuan diterima. Silakan unggah surat penerimaan KP.';
  } elseif ($penerimaanDiterima && !$laporanTerbaru) {
      $step = 'Penerimaan diterima. Silakan unggah laporan KP.';
  } elseif ($laporanTerbaru && !$statusLaporan) {
      $step = 'Laporan diunggah. Menunggu persetujuan dosen.';
  } elseif ($statusLaporan === 'ditolak') {
      $step = 'Laporan ditolak. Perbaiki sesuai catatan dosen dan unggah ulang.';
  } elseif ($statusLaporan === 'disetujui') {
      $step = 'Laporan disetujui. Proses KP selesai.';
  }
@endphp

<div class="row">
  <div class="col-lg-12 mb-3">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title mb-1">Selamat Datang, {{ $user?->name ?? 'Mahasiswa' }}</h4>
        <p class="text-muted mb-0">Ringkasan status Kerja Praktik kamu ada di bawah ini.</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3 mb-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="fw-semibold">Pengajuan KP</span>
          @if($statusPengajuan === 'diterima')
            <span class="badge bg-success">Diterima</span>
          @elseif($statusPengajuan === 'ditolak')
            <span class="badge bg-danger">Ditolak</span>
          @elseif($statusPengajuan === 'menunggu')
            <span class="badge bg-warning">Menunggu</span>
          @else
            <span class="badge bg-secondary">Belum Ada</span>
          @endif
        </div>
        <p class="mb-2 text-muted">Status pengajuan terakhir kamu.</p>
        <a href="{{ route('mahasiswa.pengajuan') }}" class="btn btn-sm btn-outline-primary">Lihat Pengajuan</a>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3 mb-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="fw-semibold">Penerimaan KP</span>
          @if($statusPenerimaan === 'diterima')
            <span class="badge bg-success">Diterima</span>
          @elseif($statusPenerimaan === 'ditolak')
            <span class="badge bg-danger">Ditolak</span>
          @elseif($statusPenerimaan === 'menunggu')
            <span class="badge bg-warning">Menunggu</span>
          @else
            <span class="badge bg-secondary">Belum Ada</span>
          @endif
        </div>
        <p class="mb-2 text-muted">
          @if($penerimaanDiterima)
            Periode: {{ $penerimaanDiterima->tanggal_mulai->format('d/m/Y') }} – {{ $penerimaanDiterima->tanggal_selesai->format('d/m/Y') }}
          @else
            Status penerimaan terakhir kamu.
          @endif
        </p>
        <a href="{{ route('mahasiswa.penerimaan') }}" class="btn btn-sm btn-outline-primary">Lihat Penerimaan</a>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3 mb-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="fw-semibold">Laporan KP</span>
          @if($statusLaporan === 'disetujui')
            <span class="badge bg-success">Disetujui</span>
          @elseif($statusLaporan === 'ditolak')
            <span class="badge bg-danger">Ditolak</span>
          @elseif($laporanTerbaru)
            <span class="badge bg-warning">Menunggu</span>
          @else
            <span class="badge bg-secondary">Belum Ada</span>
          @endif
        </div>
        <p class="mb-2 text-muted">Status laporan KP terakhir kamu.</p>
        <a href="{{ route('mahasiswa.laporan') }}" class="btn btn-sm btn-outline-primary">Kelola Laporan</a>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-xl-3 mb-3">
    <div class="card h-100">
      <div class="card-body">
        <h6 class="card-title mb-2">Langkah Selanjutnya</h6>
        <p class="text-muted small mb-0">{{ $step }}</p>
      </div>
    </div>
  </div>

  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3">Progress KP</h5>
        <div class="d-flex justify-content-between align-items-center">
          <div class="text-center flex-fill">
            <div class="mb-2">
              @php
                $colorPengajuan = 'text-muted';
                if ($statusPengajuan === 'diterima') {
                  $colorPengajuan = 'text-success';
                } elseif ($statusPengajuan === 'ditolak') {
                  $colorPengajuan = 'text-danger';
                } elseif ($statusPengajuan === 'menunggu') {
                  $colorPengajuan = 'text-warning';
                }
              @endphp
              <i class='bx bx-file {{ $colorPengajuan }} fs-1'></i>
            </div>
            <small class="fw-semibold {{ $colorPengajuan }}">Pengajuan</small>
          </div>
          <div class="flex-shrink-0 mx-2">
            <i class='bx bx-chevron-right {{ $pengajuanDiterima ? "text-success" : "text-muted" }}'></i>
          </div>
          <div class="text-center flex-fill">
            <div class="mb-2">
              @php
                $colorPenerimaan = 'text-muted';
                if ($statusPenerimaan === 'diterima') {
                  $colorPenerimaan = 'text-success';
                } elseif ($statusPenerimaan === 'ditolak') {
                  $colorPenerimaan = 'text-danger';
                } elseif ($statusPenerimaan === 'menunggu') {
                  $colorPenerimaan = 'text-warning';
                }
              @endphp
              <i class='bx bx-envelope {{ $colorPenerimaan }} fs-1'></i>
            </div>
            <small class="fw-semibold {{ $colorPenerimaan }}">Penerimaan</small>
          </div>
          <div class="flex-shrink-0 mx-2">
            <i class='bx bx-chevron-right {{ $penerimaanDiterima ? "text-success" : "text-muted" }}'></i>
          </div>
          <div class="text-center flex-fill">
            <div class="mb-2">
              @php
                $colorLaporan = 'text-muted';
                if ($statusLaporan === 'disetujui') {
                  $colorLaporan = 'text-success';
                } elseif ($statusLaporan === 'ditolak') {
                  $colorLaporan = 'text-danger';
                } elseif ($laporanTerbaru) {
                  $colorLaporan = 'text-warning';
                }
              @endphp
              <i class='bx bx-book-content {{ $colorLaporan }} fs-1'></i>
            </div>
            <small class="fw-semibold {{ $colorLaporan }}">Laporan</small>
          </div>
          <div class="flex-shrink-0 mx-2">
            <i class='bx bx-chevron-right {{ $statusLaporan === "disetujui" ? "text-success" : "text-muted" }}'></i>
          </div>
          <div class="text-center flex-fill">
            <div class="mb-2">
              <i class='bx bx-check-circle {{ $statusLaporan === "disetujui" ? "text-success" : "text-muted" }} fs-1'></i>
            </div>
            <small class="fw-semibold {{ $statusLaporan === "disetujui" ? "text-success" : "text-muted" }}">Selesai</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
