@extends('layouts.app')

@section('content')
    <p class="text-muted small mb-2">
        <span class="text-muted">Koordinator /</span> Rekap Data KP
    </p>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Rekap Data KP Mahasiswa</h5>
            <div style="width: 300px;">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Cari NIM / Nama...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-striped table-hover align-middle" id="tableData">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;">NIM</th>
                            <th style="white-space: nowrap;">Nama</th>
                            <th style="white-space: nowrap;">Perusahaan</th>
                            <th style="white-space: nowrap;">Judul KP</th>
                            <th style="white-space: nowrap;">Pembimbing</th>
                            <th style="white-space: nowrap;">Status Laporan</th>
                            <th style="white-space: nowrap;">Nilai Akhir</th>
                            <th style="white-space: nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($rekapList as $index => $pembimbingan)
                            @php
                                $mahasiswa = $pembimbingan->mahasiswa;
                                $penerimaan = $mahasiswa->penerimaanKP->first();
                                $laporan = $mahasiswa->laporanKP->first();
                            @endphp
                            <tr class="searchable-row" data-search="{{ strtolower(($mahasiswa->nim ?? '') . ' ' . ($mahasiswa->name ?? '') . ' ' . ($penerimaan->nama_perusahaan ?? '') . ' ' . ($laporan->judul_kp_final ?? '') . ' ' . ($pembimbingan->dosenPembimbing->name ?? '')) }}">
                                <td>{{ $mahasiswa->nim ?? '-' }}</td>
                                <td>{{ $mahasiswa->name ?? '-' }}</td>
                                <td>{{ $penerimaan->nama_perusahaan ?? '-' }}</td>
                                <td>{{ $laporan->judul_kp_final ?? '-' }}</td>
                                <td>{{ $pembimbingan->dosenPembimbing->name ?? 'Belum di-assign' }}</td>
                                <td>
                                    @if($laporan)
                                        @if($laporan->status_approve === 'disetujui')
                                            <span class="badge bg-label-success">Disetujui</span>
                                        @elseif($laporan->status_approve === 'ditolak')
                                            <span class="badge bg-label-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-label-warning">Menunggu</span>
                                        @endif
                                    @else
                                        <span class="badge bg-label-secondary">Belum Upload</span>
                                    @endif
                                </td>
                                <td>
                                    @if($laporan && $laporan->nilai)
                                        <span class="badge bg-label-success">{{ number_format($laporan->nilai, 2) }}</span>
                                    @else
                                        <span class="badge bg-label-secondary">Belum Dinilai</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown" style="position: static;">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" style="position: absolute; z-index: 9999;">
                                            @if($laporan && $laporan->file_laporan)
                                                <a class="dropdown-item" href="{{ Storage::url($laporan->file_laporan) }}" target="_blank">
                                                    <i class="bx bx-show me-1"></i> Lihat File
                                                </a>
                                            @else
                                                <span class="dropdown-item disabled">
                                                    <i class="bx bx-show me-1"></i> Lihat File
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bx bx-info-circle fs-4 text-muted"></i>
                                    <p class="text-muted mb-0">Belum ada data mahasiswa yang dibimbing.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function exportToExcel() {
            const rows = document.querySelectorAll('#tableData tr');
            const csv = [];
            rows.forEach(row => {
                const cols = row.querySelectorAll('th, td');
                const rowData = [];
                cols.forEach(col => {
                    rowData.push(col.innerText.replace(/,/g, ';'));
                });
                csv.push(rowData.join(','));
            });

            const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
            const downloadLink = document.createElement('a');
            downloadLink.download = 'rekap_nilai_kp_' + new Date().toISOString().slice(0, 10) + '.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
    @endpush
@endsection