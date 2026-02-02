@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <p class="text-muted small mb-2">
        <span class="text-muted">Koordinator /</span> Data Mahasiswa
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

    <!-- Card Daftar Mahasiswa -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Mahasiswa yang Anda Koordinasi</h5>
            <div style="width: 300px;">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Cari mahasiswa...">
                </div>
            </div>
        </div>
        <div class="card-body">
            
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>Email</th>
                                                        <th>Mentor Perusahaan</th>
                            <th>Perusahaan KP</th>
                            <th>Dosen Pembimbing</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="tableBody" data-base-number="{{ $mahasiswaList->firstItem() ?? 0 }}">
                        @forelse($mahasiswaList as $index => $item)
                        @php
                            $penerimaanDiterima = $item->mahasiswa->penerimaanKP->first();
                            $namaPerusahaan = $penerimaanDiterima ? $penerimaanDiterima->nama_perusahaan : '-';
                        @endphp
                        <tr class="searchable-row" data-search="{{ strtolower($item->mahasiswa->name . ' ' . $item->mahasiswa->email . ' ' . $namaPerusahaan) }}">
                            <td class="row-number">{{ $mahasiswaList->firstItem() + $index }}</td>
                            <td>{{ $item->mahasiswa->name }}</td>
                            <td>{{ $item->mahasiswa->email }}</td>
                                <td>
                                    @if($item->mentor_perusahaan)
                                        {{ $item->mentor_perusahaan }}
                                    @else
                                        <span class="text-muted">Belum diisi</span>
                                    @endif
                                </td>
                            <td>
                                @if($penerimaanDiterima)
                                    {{ $penerimaanDiterima->nama_perusahaan }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->dosenPembimbing)
                                    <span class="badge bg-success">{{ $item->dosenPembimbing->name }}</span>
                                @else
                                    <span class="badge bg-warning">Belum Ditentukan</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalAssignDosen" 
                                            data-id="{{ $item->id }}"
                                            data-mahasiswa="{{ $item->mahasiswa->name }}"
                                            data-dosen="{{ $item->dosen_pembimbing_id }}">
                                            <i class='bx bx-user-plus me-1'></i> {{ $item->dosenPembimbing ? 'Ubah' : 'Tentukan' }} Pembimbing
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                                <td colspan="7" class="text-center">Belum ada mahasiswa yang memilih Anda sebagai koordinator</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($mahasiswaList->hasPages())
            <div class="mt-3">
                {{ $mahasiswaList->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Assign Dosen -->
<div class="modal fade" id="modalAssignDosen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formAssignDosen" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Tentukan Dosen Pembimbing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Mahasiswa</label>
                        <p id="mahasiswaName" class="form-control-plaintext"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="dosen_pembimbing_id">Pilih Dosen Pembimbing <span class="text-danger">*</span></label>
                        <select class="form-select" id="dosen_pembimbing_id" name="dosen_pembimbing_id" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosenList as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-save me-1'></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalAssignDosen');
    const form = document.getElementById('formAssignDosen');
    const mahasiswaNameEl = document.getElementById('mahasiswaName');
    const dosenSelect = document.getElementById('dosen_pembimbing_id');

    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const pembimbinganId = button.getAttribute('data-id');
        const mahasiswaName = button.getAttribute('data-mahasiswa');
        const currentDosenId = button.getAttribute('data-dosen');

        // Set form action menggunakan route named
        form.action = "{{ route('koor.assign.pembimbing', '__ID__') }}".replace('__ID__', pembimbinganId);

        // Set mahasiswa name
        mahasiswaNameEl.textContent = mahasiswaName;

        // Set selected dosen if exists
        dosenSelect.value = currentDosenId || '';
    });
});
</script>
@endpush
