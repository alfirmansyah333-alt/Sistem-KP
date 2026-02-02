@extends('layouts.app')

@section('title', 'Nilai Kerja Praktek')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <p class="text-muted small mb-2">
        <span class="text-muted">Mahasiswa /</span> Nilai KP
    </p>

    @php
        $user = auth()->user();
        $laporan = $user->laporanKP()->latest()->first();
        $penerimaan = $user->penerimaanKP()->where('status', 'diterima')->first();
        $pembimbingan = \App\Models\PembimbinganKP::where('user_id', $user->id)->first();
    @endphp

    @if($laporan)
        <div class="card mb-4">
            <h5 class="card-header">Nilai Kerja Praktek</h5>
            <div class="card-body">
                <!-- Informasi Umum -->
                <div class="mb-4">
                    <h6 class="text-muted fw-bold">Informasi Umum</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Judul KP:</strong> <span class="text-dark">{{ $laporan->judul_kp_final ?? '-' }}</span></p>
                            <p><strong>Nama Pembimbing:</strong> <span class="text-dark">{{ $pembimbingan?->dosenPembimbing?->name ?? '-' }}</span></p>
                            @if($pembimbingan?->mentor_perusahaan)
                            <p><strong>Mentor Perusahaan:</strong> <span class="text-dark">{{ $pembimbingan->mentor_perusahaan }}</span></p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Perusahaan:</strong> <span class="text-dark">{{ $penerimaan?->nama_perusahaan ?? '-' }}</span></p>
                            <p><strong>Periode KP:</strong> <span class="text-dark">
                                @if($penerimaan)
                                    {{ $penerimaan->tanggal_mulai->format('d/m/Y') }} – {{ $penerimaan->tanggal_selesai->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </span></p>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Status Laporan & Nilai -->
                @if($laporan->status_approve === 'disetujui' && $laporan->nilai)
                    <div class="text-center my-5">
                        <h6 class="text-muted mb-3">Nilai Akhir dari Pembimbing</h6>
                        <h1 class="fw-bold text-success display-4">{{ number_format($laporan->nilai, 2) }}</h1>
                    </div>
                @elseif($laporan->status_approve === 'ditolak')
                    <div class="alert alert-danger">
                        <h6><i class='bx bx-x-circle me-2'></i> Laporan Ditolak</h6>
                        @if($laporan->catatan_dosen)
                        <p class="mb-0">Alasan: {{ $laporan->catatan_dosen }}</p>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning">
                        <h6><i class='bx bx-time-five me-2'></i> Menunggu Persetujuan & Penilaian</h6>
                        <p class="mb-0">Laporan Anda sedang menunggu persetujuan dan penilaian dari pembimbing.</p>
                    </div>
                @endif

            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class='bx bx-info-circle me-2'></i>
            Anda belum mengunggah laporan KP. <a href="{{ route('mahasiswa.laporan') }}">Unggah laporan di sini</a>.
        </div>
    @endif

</div>
@endsection
