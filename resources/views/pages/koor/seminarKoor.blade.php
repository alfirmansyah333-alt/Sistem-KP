@extends('layouts.app')

@section('title', 'Pendaftaran Seminar KP')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <p class="text-muted small mb-2">
        <span class="text-muted">Koordinator /</span> Seminar KP
    </p>
    
    {{-- Modal Setujui Seminar dengan Input Jadwal --}}
    <div class="modal fade" id="modalApprove" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formApprove" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="disetujui">
                    
                    <div class="modal-header">
                        <h5 class="modal-title text-success">
                            <i class="bx bx-check-circle me-2"></i>Setujui Pendaftaran Seminar
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Apakah Anda yakin ingin menyetujui pendaftaran seminar ini?</p>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan tambahan untuk mahasiswa..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-check me-1"></i>Ya, Setujui Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tolak Seminar --}}
    <div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formReject" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="ditolak">
                    
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i class="bx bx-x-circle me-2"></i>Tolak Pendaftaran Seminar
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Apakah Anda yakin ingin menolak pendaftaran seminar ini?</p>
                        
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Berikan alasan penolakan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-x me-1"></i>Ya, Tolak Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Hapus Seminar --}}
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i class="bx bx-trash me-2"></i>Hapus Pendaftaran Seminar
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Apakah Anda yakin ingin menghapus pendaftaran seminar ini?</p>
                        <p class="text-muted small">Data yang dihapus tidak dapat dikembalikan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-trash me-1"></i>Ya, Hapus Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pendaftaran Seminar KP</h5>
            <div style="width: 300px;">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Cari NIM, nama, atau judul...">
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
                            <th style="white-space: nowrap;">No</th>
                            <th style="white-space: nowrap;">NIM</th>
                            <th style="white-space: nowrap;">Nama</th>
                            <th style="white-space: nowrap;">Judul KP</th>
                            <th style="white-space: nowrap;">Tanggal Daftar</th>
                            <th style="white-space: nowrap;">Jadwal Seminar</th>
                            <th style="white-space: nowrap;">Jam</th>
                            <th style="white-space: nowrap;">Ruangan</th>
                            <th style="white-space: nowrap;">Status</th>
                            <th style="white-space: nowrap;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" data-base-number="{{ $seminarList->firstItem() ?? 0 }}">
                        @php($baseNumber = $seminarList->firstItem())
                        @forelse($seminarList as $index => $seminar)
                            <tr class="searchable-row"
                                data-search="{{ strtolower($seminar->user->nim ?? '') }} {{ strtolower($seminar->user->name ?? '') }} {{ strtolower($seminar->judul_kp) }}">
                                <td class="row-number">{{ $baseNumber + $index }}</td>
                                <td>{{ $seminar->user->nim ?? '-' }}</td>
                                <td>{{ $seminar->user->name ?? '-' }}</td>
                                <td>{{ $seminar->judul_kp }}</td>
                                <td>{{ $seminar->created_at->format('d/m/Y') }}</td>
                                <td>{{ optional($seminar->tanggal_seminar)->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $seminar->jam_seminar ?? '-' }}</td>
                                <td>{{ $seminar->ruangan ?? '-' }}</td>
                                <td>
                                    @if($seminar->status == 'menunggu')
                                        <span class="badge bg-label-warning">Menunggu</span>
                                    @elseif($seminar->status == 'disetujui')
                                        <span class="badge bg-label-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-label-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown" style="position: static;">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" style="position: absolute; z-index: 9999;">
                                            @if($seminar->status == 'menunggu')
                                                <button type="button" class="dropdown-item text-success"
                                                    onclick="openApproveModal({{ $seminar->id }})">
                                                    <i class="bx bx-check me-1"></i> Setujui
                                                </button>
                                                <button type="button" class="dropdown-item text-danger"
                                                    onclick="openRejectModal({{ $seminar->id }})">
                                                    <i class="bx bx-x me-1"></i> Tolak
                                                </button>
                                            @else
                                                <button type="button" class="dropdown-item text-danger"
                                                    onclick="openDeleteModal({{ $seminar->id }})">
                                                    <i class="bx bx-trash me-1"></i> Hapus
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bx bx-file-blank fs-1 d-block mb-2"></i>
                                        Belum ada pendaftaran seminar KP dari mahasiswa
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $seminarList->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    function openApproveModal(seminarId) {
        const form = document.getElementById('formApprove');
        form.action = `/koor/seminar/${seminarId}/status`;
        var modal = new bootstrap.Modal(document.getElementById('modalApprove'));
        modal.show();
    }

    function openRejectModal(seminarId) {
        const form = document.getElementById('formReject');
        form.action = `/koor/seminar/${seminarId}/status`;
        var modal = new bootstrap.Modal(document.getElementById('modalReject'));
        modal.show();
    }

    function openDeleteModal(seminarId) {
        const form = document.getElementById('formDelete');
        form.action = `/koor/seminar/${seminarId}`;
        var modal = new bootstrap.Modal(document.getElementById('modalDelete'));
        modal.show();
    }
</script>
@endsection