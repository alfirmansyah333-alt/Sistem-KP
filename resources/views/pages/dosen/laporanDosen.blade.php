@extends('layouts.app')

@section('title', 'Laporan KP Mahasiswa Bimbingan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <p class="text-muted small mb-2">
        <span class="text-muted">Dosen /</span> Laporan KP Mahasiswa Bimbingan
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

    <!-- Card Daftar Laporan KP -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Laporan KP Mahasiswa yang Anda Bimbing</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama mahasiswa atau judul KP...">
            </div>
            
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;">No</th>
                            <th style="white-space: nowrap;">Nama Mahasiswa</th>
                            <th style="white-space: nowrap;">NIM</th>
                            <th style="white-space: nowrap;">Judul KP Final</th>
                            <th style="white-space: nowrap;">Tanggal Upload</th>
                            <th style="white-space: nowrap;">Status</th>
                            <th style="white-space: nowrap;">Nilai</th>
                            <th style="white-space: nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="tableBody" data-base-number="{{ $laporanList->firstItem() ?? 0 }}">
                        @forelse($laporanList as $index => $laporan)
                        <tr class="searchable-row" data-search="{{ strtolower(($laporan->mahasiswa->nim ?? '') . ' ' . $laporan->mahasiswa->name . ' ' . $laporan->judul_kp_final) }}">
                            <td class="row-number">{{ $laporanList->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $laporan->mahasiswa->name }}</strong>
                            </td>
                            <td>{{ $laporan->mahasiswa->nim ?? '-' }}</td>
                            <td>{{ $laporan->judul_kp_final }}</td>
                            </td>
                            <td>{{ $laporan->tanggal_upload ? $laporan->tanggal_upload->setTimezone('Asia/Jakarta')->format('d/m/Y') : '-' }}</td>
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
                                @if($laporan->nilai)
                                    <span class="badge bg-info">{{ number_format($laporan->nilai, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('dosen.laporan.view', $laporan->id) }}" target="_blank">
                                            <i class='bx bx-show me-1'></i> Lihat
                                        </a>
                                        <a class="dropdown-item" href="{{ route('dosen.laporan.download', $laporan->id) }}">
                                            <i class='bx bx-download me-1'></i> Download
                                        </a>
                                        @if($laporan->status_approve === 'menunggu' || $laporan->status_approve === 'ditolak')
                                            <button type="button" class="dropdown-item text-primary"
                                                data-bs-toggle="modal" data-bs-target="#modalApprove{{ $laporan->id }}">
                                                <i class='bx bx-sync me-1'></i> Ubah Status
                                            </button>
                                        @endif
                                        @if($laporan->status_approve === 'disetujui' && !$laporan->nilai)
                                            <button type="button" class="dropdown-item text-warning"
                                                data-bs-toggle="modal" data-bs-target="#modalNilai{{ $laporan->id }}">
                                                <i class='bx bx-edit me-1'></i> Nilai
                                            </button>
                                        @endif
                                        <button type="button" class="dropdown-item text-danger"
                                            data-bs-toggle="modal" data-bs-target="#modalDelete{{ $laporan->id }}">
                                            <i class='bx bx-trash me-1'></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Approve -->
                        <div class="modal fade" id="modalApprove{{ $laporan->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('dosen.laporan.updateStatus', $laporan->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Persetujuan Laporan KP</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Status Persetujuan</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status_approve" id="approve_yes{{ $laporan->id }}" value="disetujui" required>
                                                    <label class="form-check-label" for="approve_yes{{ $laporan->id }}">
                                                        Setujui Laporan
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status_approve" id="approve_no{{ $laporan->id }}" value="ditolak">
                                                    <label class="form-check-label" for="approve_no{{ $laporan->id }}">
                                                        Tolak Laporan
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="catatan_approve{{ $laporan->id }}" class="form-label">Catatan (Opsional)</label>
                                                <textarea class="form-control" id="catatan_approve{{ $laporan->id }}" name="catatan_dosen" rows="3" placeholder="Masukkan catatan atau alasan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Persetujuan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Nilai -->
                        <div class="modal fade" id="modalNilai{{ $laporan->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('dosen.laporan.updateNilai', $laporan->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Pemberian Nilai Laporan KP</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="nilai_input{{ $laporan->id }}" class="form-label fw-bold">Nilai (0-100)</label>
                                                <input type="number" class="form-control" 
                                                    id="nilai_input{{ $laporan->id }}" name="nilai" min="0" max="100" step="0.01" 
                                                    placeholder="Masukkan nilai..." required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Delete -->
                        <div class="modal fade" id="modalDelete{{ $laporan->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('dosen.laporan.destroy', $laporan->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Hapus Laporan KP</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-danger">
                                                <i class='bx bx-error-circle me-2'></i>
                                                <strong>Perhatian!</strong> Data laporan akan dihapus secara permanen!
                                            </div>
                                            <p>Mahasiswa: <strong>{{ $laporan->mahasiswa->name }}</strong></p>
                                            <p>Judul KP: <strong>{{ $laporan->judul_kp_final }}</strong></p>
                                            <p>Status saat ini: 
                                                @if($laporan->status_approve === 'disetujui')
                                                    <span class="badge bg-success">Disetujui</span>
                                                @elseif($laporan->status_approve === 'ditolak')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-warning">Menunggu</span>
                                                @endif
                                            </p>
                                            <p class="text-muted">Data laporan akan dihapus dan mahasiswa harus mengupload ulang laporan KP dari awal.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Ya, Hapus Laporan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada laporan KP dari mahasiswa bimbingan Anda</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($laporanList->hasPages())
            <div class="mt-3">
                {{ $laporanList->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize search
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('tableBody');
        const rows = tableBody.querySelectorAll('tr.searchable-row');

        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();

            rows.forEach(row => {
                const searchText = row.getAttribute('data-search');
                row.style.display = searchText.includes(filter) ? '' : 'none';
            });
        });
    });
</script>
<style>
    .table-responsive {
        overflow-x: auto;
    }
    .table thead th {
        white-space: nowrap;
    }
    .dropdown {
        position: static !important;
    }
    .dropdown-menu {
        position: absolute !important;
        z-index: 9999 !important;
    }
</style>
@endpush
@endsection
