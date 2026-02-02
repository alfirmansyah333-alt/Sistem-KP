@extends('layouts.app')

@section('title', 'Detail Pengajuan KP')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Detail Pengajuan Kerja Praktek - {{ $pengajuan->user->name }}</h4>
                        <a href="{{ route('koor.pengajuan') }}" class="btn btn-secondary btn-sm rounded-pill">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Informasi Mahasiswa -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-3">Informasi Mahasiswa</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="20%"><strong>Nama</strong></td>
                                        <td>: {{ $pengajuan->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>NIM</strong></td>
                                        <td>: {{ $pengajuan->user->nim ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email</strong></td>
                                        <td>: {{ $pengajuan->user->email }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <!-- Accordion - Semua Pengajuan -->
                        @php
                            $allPengajuan = $pengajuan->user->pengajuanKP()->get();
                        @endphp
                        <h6 class="text-muted mb-3">Daftar Semua Pengajuan</h6>
                        <div class="accordion mb-4" id="accordionPengajuan">
                            @forelse($allPengajuan as $index => $p)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $p->id }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $p->id }}" 
                                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" 
                                            aria-controls="collapse{{ $p->id }}">
                                            <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                                <div>
                                                    <strong>{{ $p->perusahaan_tujuan }}</strong>
                                                    <small class="text-muted d-block">Periode {{ $p->periode ?? '-' }} | {{ $p->tanggal_pengajuan->format('d/m/Y') }}</small>
                                                </div>
                                                <span class="badge bg-label-{{ $p->status == 'diterima' ? 'success' : ($p->status == 'ditolak' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($p->status) }}
                                                </span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $p->id }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                                        aria-labelledby="heading{{ $p->id }}" data-bs-parent="#accordionPengajuan">
                                        <div class="accordion-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <h6 class="text-muted mb-2">Detail Pengajuan</h6>
                                                    <table class="table table-borderless table-sm">
                                                        <tr>
                                                            <td width="30%"><strong>Periode</strong></td>
                                                            <td>: {{ $p->periode ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Tanggal Pengajuan</strong></td>
                                                            <td>: {{ $p->tanggal_pengajuan->format('d/m/Y') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Status</strong></td>
                                                            <td>: 
                                                                <span class="badge bg-label-{{ $p->status == 'diterima' ? 'success' : ($p->status == 'ditolak' ? 'danger' : 'warning') }}">
                                                                    {{ ucfirst($p->status) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-muted mb-2">Data Perusahaan</h6>
                                                    <table class="table table-borderless table-sm">
                                                        <tr>
                                                            <td width="30%"><strong>Perusahaan</strong></td>
                                                            <td>: {{ $p->perusahaan_tujuan }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Mitra</strong></td>
                                                            <td>: 
                                                                <span class="badge bg-label-{{ $p->mitra_dengan_perusahaan == 'iya' ? 'success' : 'secondary' }}">
                                                                    {{ $p->mitra_dengan_perusahaan == 'iya' ? 'Ya' : 'Tidak' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <hr>
                                            <h6 class="text-muted mb-2">Dokumen</h6>
                                            @if($p->file_surat_pengajuan)
                                                <a href="{{ Storage::url($p->file_surat_pengajuan) }}" target="_blank"
                                                    class="btn btn-sm btn-primary rounded-pill me-2">
                                                    <i class="bx bx-file-blank me-1"></i> Surat Pengajuan
                                                </a>
                                            @else
                                                <span class="text-muted small">Tidak ada file pengajuan</span>
                                            @endif
                                            @if($p->file_surat_penerimaan)
                                                <a href="{{ Storage::url($p->file_surat_penerimaan) }}" target="_blank"
                                                    class="btn btn-sm btn-success rounded-pill">
                                                    <i class="bx bx-file-blank me-1"></i> Surat Penerimaan
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    Belum ada pengajuan KP dari mahasiswa ini.
                                </div>
                            @endforelse
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <strong>Informasi:</strong> Mahasiswa yang mengelola status pengajuannya sendiri. Koordinator hanya dapat melihat data pengajuan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection