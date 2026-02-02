@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Mahasiswa KP</h5>
            <div style="width: 300px;">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Cari nama / NIM...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover" id="tableData">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Perusahaan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Pembimbing</th>
                        <th>Judul KP</th>
                        <th>Tanggal Seminar</th>
                        <th>Nilai KP</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0" id="tableBody" data-base-number="{{ $mahasiswaList->firstItem() ?? 0 }}">
                    @forelse($mahasiswaList as $mhs)
                        @php
                            $penerimaan = $mhs->penerimaanKP->first();
                            $laporan = $mhs->laporanKP->first();
                            $seminar = $mhs->seminarKP->first();
                            $pembimbing = $mhs->pembimbingan?->dosenPembimbing?->name;
                            $tanggalMulai = $penerimaan?->tanggal_mulai ? $penerimaan->tanggal_mulai->format('d M Y') : '-';
                            $tanggalSelesai = $penerimaan?->tanggal_selesai ? $penerimaan->tanggal_selesai->format('d M Y') : '-';
                            $tanggalSeminar = $seminar?->tanggal_seminar ? $seminar->tanggal_seminar->format('d M Y') : '-';
                            $nilai = $laporan?->nilai ? number_format($laporan->nilai, 2) : '-';
                        @endphp
                        <tr class="searchable-row"
                            data-search="{{ strtolower(($mhs->nim ?? '') . ' ' . ($mhs->name ?? '') . ' ' . ($penerimaan->nama_perusahaan ?? '') . ' ' . ($tanggalMulai ?? '') . ' ' . ($tanggalSelesai ?? '') . ' ' . ($pembimbing ?? '') . ' ' . ($laporan->judul_kp_final ?? '') . ' ' . ($tanggalSeminar ?? '') . ' ' . ($nilai ?? '')) }}">
                            <td class="row-number">{{ $loop->iteration + ($mahasiswaList->firstItem() ? $mahasiswaList->firstItem() - 1 : 0) }}</td>
                            <td>{{ $mhs->nim ?? '-' }}</td>
                            <td>{{ $mhs->name ?? '-' }}</td>
                            <td>{{ $penerimaan->nama_perusahaan ?? '-' }}</td>
                            <td>{{ $tanggalMulai }}</td>
                            <td>{{ $tanggalSelesai }}</td>
                            <td>{{ $pembimbing ?? '-' }}</td>
                            <td>{{ $laporan->judul_kp_final ?? '-' }}</td>
                            <td>{{ $tanggalSeminar }}</td>
                            <td>{{ $nilai }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                Belum ada data mahasiswa KP.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                    </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a class="btn btn-sm btn-outline-primary rounded-pill" href="{{ route('admin.data-mahasiswa.export') }}">
                    <i class="bx bx-download"></i> Download Excel
                </a>
            </div>
        </div>

        @if($mahasiswaList->hasPages())
            <div class="mt-3">
                {{ $mahasiswaList->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    @endpush
@endsection