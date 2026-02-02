@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Perusahaan KP</h5>
            <div class="d-flex gap-2">
                <div style="width: 300px;">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Cari perusahaan...">
                    </div>
                </div>
                <a href="{{ route('admin.perusahaan.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Perusahaan
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-hover align-middle" id="tableData">
                <thead>
                    <tr>
                        <th style="white-space: nowrap;">No</th>
                        <th style="white-space: nowrap;">Nama Perusahaan</th>
                        <th style="white-space: nowrap;">Mitra</th>
                        <th style="white-space: nowrap;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($perusahaanList as $index => $perusahaan)
                        <tr class="searchable-row" data-search="{{ strtolower($perusahaan->nama_perusahaan ?? '') }}">
                            <td>{{ $perusahaanList->firstItem() + $index }}</td>
                            <td>{{ $perusahaan->nama_perusahaan ?? '-' }}</td>
                            <td>
                                @if($perusahaan->is_mitra)
                                    <span class="badge bg-label-success">Ya</span>
                                @else
                                    <span class="badge bg-label-secondary">Tidak</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown" style="position: static;">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" style="position: absolute; z-index: 9999;">
                                        <a class="dropdown-item" href="{{ route('admin.perusahaan.edit', $perusahaan->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i>Edit
                                        </a>
                                        <a class="dropdown-item text-danger" href="#" onclick="if(confirm('Yakin hapus perusahaan ini?')) { document.getElementById('deleteForm{{ $perusahaan->id }}').submit(); }">
                                            <i class="bx bx-trash me-1"></i>Hapus
                                        </a>
                                        <form id="deleteForm{{ $perusahaan->id }}" action="{{ route('admin.perusahaan.destroy', $perusahaan->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Belum ada data perusahaan KP.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                    </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    {{ $perusahaanList->links() }}
                </div>
                <a class="btn btn-sm btn-outline-primary rounded-pill" href="{{ route('admin.data-perusahaan.export') }}">
                    <i class="bx bx-download"></i> Download Excel
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('tableBody');
        const rows = tableBody.querySelectorAll('.searchable-row');

        searchInput?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                if (searchData.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
    @endpush
@endsection