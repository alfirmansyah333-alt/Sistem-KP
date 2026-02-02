@extends('layouts.app')

@section('title', 'Pengajuan KP Mahasiswa')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <p class="text-muted small mb-2">
        <span class="text-muted">Koordinator /</span> Pengajuan KP
    </p>
    
    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bx bx-trash me-2"></i>Konfirmasi Hapus Pengajuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bx bx-trash text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h6 class="text-center mb-3">PERINGATAN</h6>
                    <p class="text-center mb-0">
                        Data pengajuan yang dihapus <strong class="text-danger">TIDAK DAPAT</strong> dikembalikan.<br>
                        Apakah Anda yakin ingin menghapus pengajuan ini?
                    </p>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                        <i class="bx bx-check me-1"></i>Ya, Hapus Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengajuan KP Mahasiswa</h5>
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

            <!-- Hidden data untuk pencarian: semua pengajuan dalam JSON -->
            <div id="allPengajuanData" data-pengajuan="{{ json_encode($allPengajuan->map(function($p) { return ['id' => $p->id, 'nim' => strtolower($p->user->nim ?? ''), 'nama' => strtolower($p->user->name), 'perusahaan' => strtolower($p->perusahaan_tujuan)]; })) }}" style="display: none;"></div>

            @php
                $baseNumber = $pengajuanList->firstItem() ?? 1;
                $groupedPengajuan = $pengajuanList->getCollection()->groupBy('user_id');
                $pengajuanPerMahasiswa = $groupedPengajuan->map(function ($items) {
                    $user = $items->first()->user;
                    $all = $user->pengajuanKP()->get();
                    $diterima = $all->where('status', 'diterima')->first();
                    $priority = $diterima ?? $all->first();

                    return [
                        'user' => $user,
                        'all' => $all,
                        'diterima' => $diterima,
                        'priority' => $priority,
                    ];
                });
            @endphp

            <div class="table-responsive" style="overflow-x: auto; overflow-y: visible;">
                <table class="table table-hover align-middle" id="tableData">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Periode</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Perusahaan Tujuan</th>
                            <th>Mitra</th>
                            <th>Status Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" data-base-number="{{ $pengajuanList->firstItem() ?? 0 }}">
                        @forelse($pengajuanPerMahasiswa as $row)
                            @php
                                $user = $row['user'];
                                $allUserPengajuan = $row['all'];
                                $pengajuanDiterima = $row['diterima'];
                                $priorityPengajuan = $row['priority'];
                            @endphp
                            <tr class="searchable-row"
                                data-pengajuan-id="{{ $priorityPengajuan->id }}"
                                data-search="{{ strtolower($user->nim ?? '') }} {{ strtolower($user->name) }} {{ strtolower($priorityPengajuan->perusahaan_tujuan ?? '') }}">
                                <td class="row-number">{{ $baseNumber + $loop->index }}</td>
                                <td><span class="badge bg-label-info">Periode {{ $priorityPengajuan->periode ?? '-' }}</span></td>
                                <td>{{ $user->nim ?? '-' }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $priorityPengajuan->perusahaan_tujuan }}</td>
                                <td>
                                    @if($priorityPengajuan->mitra_dengan_perusahaan == 'iya')
                                        <span class="badge bg-label-success">Ya</span>
                                    @else
                                        <span class="badge bg-label-secondary">Tidak</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColor = [
                                            'diterima' => 'success',
                                            'ditolak' => 'danger',
                                            'menunggu' => 'warning'
                                        ];
                                    @endphp
                                    @if($pengajuanDiterima)
                                        <span class="badge bg-label-{{ $statusColor[$pengajuanDiterima->status] ?? 'secondary' }}">
                                            {{ ucfirst($pengajuanDiterima->status) }}
                                        </span>
                                    @else
                                        <span class="badge bg-label-{{ $statusColor[$priorityPengajuan->status] ?? 'secondary' }}">
                                            {{ ucfirst($priorityPengajuan->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{ route('koor.pengajuan.show', $priorityPengajuan->id) }}" class="dropdown-item">
                                                <i class="bx bx-show me-1"></i> Lihat Semua Pengajuan
                                            </a>
                                            <form id="formDelete{{ $priorityPengajuan->id }}"
                                                action="{{ route('koor.pengajuan.destroy', $priorityPengajuan->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item text-danger"
                                                    onclick="openDeleteModal({{ $priorityPengajuan->id }})">
                                                    <i class="bx bx-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bx bx-file-blank fs-1 d-block mb-2"></i>
                                        Belum ada pengajuan KP dari mahasiswa
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $pengajuanList->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .table-responsive .dropdown-menu {
        position: fixed;
        z-index: 9999;
    }
</style>

<script>
    // Script modal konfirmasi hapus
    let currentDeleteId = null;

    function openDeleteModal(pengajuanId) {
        currentDeleteId = pengajuanId;
        var deleteModal = new bootstrap.Modal(document.getElementById('modalConfirmDelete'));
        deleteModal.show();
    }

    document.getElementById('btnConfirmDelete').addEventListener('click', function () {
        if (currentDeleteId) {
            document.getElementById('formDelete' + currentDeleteId).submit();
        }
    });
</script>
@endsection
