@extends('layouts.app')

@section('title', 'Penerimaan KP')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <p class="text-muted small mb-2">
            <span class="text-muted">Mahasiswa /</span> Penerimaan KP
        </p>
        
        @php
            $hasDiterima = auth()->user()->penerimaanKP()->where('status', 'diterima')->exists();
        @endphp
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Penerimaan Kerja Praktek</h5>
                <!-- Tombol Tambah Penerimaan -->
                <a href="{{ route('mahasiswa.penerimaan.create') }}" 
                   class="btn btn-primary rounded-pill {{ $hasDiterima ? 'disabled' : '' }}"
                   {{ $hasDiterima ? 'aria-disabled=true' : '' }}>
                    <i class="bx bx-plus me-1"></i> Ajukan Penerimaan KP
                </a>
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

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="white-space: nowrap; width: 5%;">No</th>
                                <th style="white-space: nowrap; width: 20%;">Nama Perusahaan</th>
                                <th style="white-space: nowrap; width: 12%;">Tanggal Mulai</th>
                                <th style="white-space: nowrap; width: 12%;">Tanggal Selesai</th>
                                <th style="white-space: nowrap; width: 18%;">Surat Penerimaan</th>
                                <th style="white-space: nowrap; width: 12%;">Status</th>
                                <th style="white-space: nowrap; width: 14%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($penerimaanList as $index => $penerimaan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $penerimaan->nama_perusahaan }}</td>
                                <td>{{ $penerimaan->tanggal_mulai->format('d/m/Y') }}</td>
                                <td>{{ $penerimaan->tanggal_selesai->format('d/m/Y') }}</td>
                                    <td>
                                        @if($penerimaan->file_surat_penerimaan)
                                            <a href="{{ Storage::url($penerimaan->file_surat_penerimaan) }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm rounded-pill">
                                                <i class="bx bx-file me-1"></i> Lihat File
                                            </a>
                                        @else
                                            <span class="text-muted">Belum upload</span>
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
                                        @if($penerimaan->status == 'menunggu')
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ route('mahasiswa.penerimaan.edit', $penerimaan->id) }}" class="dropdown-item">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    <form id="formDelete{{ $penerimaan->id }}"
                                                        action="{{ route('mahasiswa.penerimaan.destroy', $penerimaan->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item text-danger"
                                                            onclick="confirmDelete({{ $penerimaan->id }})">
                                                            <i class="bx bx-trash me-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @elseif($penerimaan->status == 'ditolak')
                                            <a href="{{ route('mahasiswa.penerimaan.edit', $penerimaan->id) }}" class="btn btn-sm btn-warning">
                                                <i class="bx bx-edit me-1"></i> Edit & Upload Ulang
                                            </a>
                                        @else
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" disabled>
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-file-blank fs-1 d-block mb-2"></i>
                                            Belum ada data penerimaan KP
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

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

    <script>
        function confirmDelete(penerimaanId) {
            if (confirm('Apakah Anda yakin ingin menghapus penerimaan KP ini?')) {
                document.getElementById('formDelete' + penerimaanId).submit();
            }
        }
    </script>
@endsection