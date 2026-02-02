@extends('layouts.app')

@section('title', 'Penerimaan KP Mahasiswa')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <p class="text-muted small mb-2">
        <span class="text-muted">Koordinator /</span> Penerimaan KP
    </p>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bx bx-trash me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data penerimaan KP ini?</p>
                    <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="formDelete" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-trash me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Surat Penerimaan KP</h5>
            <div style="width: 300px;">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Cari NIM, nama, atau perusahaan...">
                </div>
            </div>
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

            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-hover align-middle" id="tableData">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%; white-space: nowrap;">No</th>
                            <th style="width: 8%; white-space: nowrap;">NIM</th>
                            <th style="width: 12%; white-space: nowrap;">Nama</th>
                            <th style="width: 18%; white-space: nowrap;">Perusahaan</th>
                            <th style="width: 10%; white-space: nowrap;">Tgl Mulai</th>
                            <th style="width: 10%; white-space: nowrap;">Tgl Selesai</th>
                            <th style="width: 16%; white-space: nowrap;">File Surat</th>
                            <th style="width: 10%; white-space: nowrap;">Status</th>
                            <th style="width: 11%; white-space: nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" data-base-number="{{ $penerimaanList->firstItem() ?? 0 }}">
                        @php($baseNumber = $penerimaanList->firstItem())
                        @forelse($penerimaanList as $index => $penerimaan)
                            <tr class="searchable-row"
                                data-search="{{ strtolower($penerimaan->user->nim ?? '') }} {{ strtolower($penerimaan->user->name ?? '') }} {{ strtolower($penerimaan->nama_perusahaan ?? ($penerimaan->pengajuanKP->perusahaan_tujuan ?? '')) }}">
                                <td class="row-number">{{ $baseNumber + $index }}</td>
                                <td>{{ $penerimaan->user->nim ?? '-' }}</td>
                                <td>{{ $penerimaan->user->name ?? '-' }}</td>
                                <td>{{ $penerimaan->nama_perusahaan ?? ($penerimaan->pengajuanKP->perusahaan_tujuan ?? '-') }}</td>
                                <td>{{ optional($penerimaan->tanggal_mulai)->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ optional($penerimaan->tanggal_selesai)->format('d/m/Y') ?? '-' }}</td>
                                <td>
                                    @if($penerimaan->file_surat_penerimaan)
                                        <a href="{{ Storage::url($penerimaan->file_surat_penerimaan) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill">
                                            <i class="bx bx-file me-1"></i> Lihat File
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($penerimaan->status == 'menunggu')
                                        <span class="badge bg-label-warning">Menunggu</span>
                                    @elseif($penerimaan->status == 'diterima')
                                        <span class="badge bg-label-success">Diterima</span>
                                    @else
                                        <span class="badge bg-label-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @if($penerimaan->status == 'menunggu')
                                                <form action="{{ route('koor.penerimaan.updateStatus', $penerimaan->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="diterima">
                                                    <button type="submit" class="dropdown-item text-success">
                                                        <i class="bx bx-check me-1"></i> Setujui
                                                    </button>
                                                </form>
                                                <form action="{{ route('koor.penerimaan.updateStatus', $penerimaan->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="ditolak">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bx bx-x me-1"></i> Tolak
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" class="dropdown-item text-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalConfirmDelete"
                                                data-id="{{ $penerimaan->id }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bx bx-file-blank fs-1 d-block mb-2"></i>
                                        Belum ada data surat penerimaan KP
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $penerimaanList->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalConfirmDelete');
    const form = document.getElementById('formDelete');
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const penerimaanId = button.getAttribute('data-id');
        form.action = `/koor/penerimaan/${penerimaanId}`;
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