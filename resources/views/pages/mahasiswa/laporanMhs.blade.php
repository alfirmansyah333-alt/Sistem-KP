@extends('layouts.app')

@section('title', 'Upload Laporan Akhir KP')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <p class="text-muted small mb-2">
        <span class="text-muted">Mahasiswa /</span> Laporan KP
    </p>
    
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

    @if(!$seminarDisetujui)
        <div class="alert alert-warning" role="alert">
            <i class='bx bx-info-circle me-2'></i>
            <strong>Perhatian!</strong> Anda harus menyelesaikan seminar KP terlebih dahulu sebelum dapat mengupload laporan KP.
        </div>
    @endif

    <!-- Tabel Laporan KP -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Laporan KP</h5>
            @if($seminarDisetujui && (!$laporan || $laporan->status_approve !== 'disetujui'))
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUploadLaporan">
                    <i class='bx bx-plus me-1'></i> {{ $laporan && $laporan->status_approve === 'ditolak' ? 'Upload Laporan Baru' : 'Upload Laporan KP' }}
                </button>
            @endif
        </div>
        <div class="card-body">
            @if($laporan)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;">Judul KP Final</th>
                            <th style="white-space: nowrap;">Tanggal Upload</th>
                            <th style="white-space: nowrap;">Status</th>
                            <th style="white-space: nowrap;">Catatan</th>
                            <th style="white-space: nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $laporan->judul_kp_final }}</td>
                            <td>{{ $laporan->tanggal_upload ? $laporan->tanggal_upload->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @if($laporan->status_approve === 'disetujui')
                                    <span class="badge bg-success">✓ Disetujui</span>
                                @elseif($laporan->status_approve === 'ditolak')
                                    <span class="badge bg-danger">✗ Ditolak</span>
                                @else
                                    <span class="badge bg-warning">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                @if($laporan->catatan_dosen)
                                    <small>{{ Str::limit($laporan->catatan_dosen, 50) }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if($laporan->file_laporan)
                                    <a href="{{ Storage::url($laporan->file_laporan) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class='bx bx-show'></i>
                                    </a>
                                    @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                        <i class='bx bx-show'></i>
                                    </button>
                                    @endif
                                    @if($laporan->status_approve !== 'disetujui' && $laporan->status_approve !== 'ditolak')
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalUploadLaporan">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @else
            <div class="alert alert-info">
                <i class='bx bx-info-circle me-2'></i>
                Belum ada laporan KP yang diupload. Silakan upload laporan KP Anda.
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Upload Laporan -->
    <div class="modal fade" id="modalUploadLaporan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('mahasiswa.laporan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if($laporan && $laporan->status_approve === 'ditolak')
                                Upload Laporan Baru
                            @else
                                Upload Laporan KP
                            @endif
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if($laporan && $laporan->status_approve === 'ditolak')
                        <div class="alert alert-warning">
                            <i class='bx bx-info-circle me-2'></i>
                            Laporan sebelumnya ditolak. Silakan upload laporan baru yang sudah diperbaiki sesuai catatan dosen.
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="judulKPFinal" class="form-label">Judul KP Final</label>
                            <input 
                                type="text" 
                                class="form-control @error('judul_kp_final') is-invalid @enderror" 
                                id="judulKPFinal" 
                                name="judul_kp_final" 
                                value="{{ $laporan->judul_kp_final ?? '' }}"
                                placeholder="Masukkan judul KP final"
                                required
                            >
                            @error('judul_kp_final')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="laporanAkhir" class="form-label">Upload Laporan Akhir (PDF) 
                                @if($laporan && $laporan->status_approve === 'ditolak')
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <input 
                                class="form-control @error('laporan_akhir') is-invalid @enderror" 
                                type="file" 
                                id="laporanAkhir"  
                                name="laporan_akhir" 
                                accept=".pdf"
                                @if($laporan && $laporan->status_approve === 'ditolak') required @endif
                            >
                            @error('laporan_akhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                @if($laporan && $laporan->status_approve === 'ditolak')
                                    Wajib upload file baru yang sudah diperbaiki.
                                @else
                                    Kosongkan jika hanya ingin update judul. Pastikan laporan sudah lengkap dengan lembar pengesahan & tanda tangan.
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-upload me-1'></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
