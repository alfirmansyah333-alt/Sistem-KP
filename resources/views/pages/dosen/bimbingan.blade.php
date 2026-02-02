@extends('layouts.app')

@section('title', 'Mahasiswa Bimbingan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <p class="text-muted small mb-2">
        <span class="text-muted">Dosen /</span> Mahasiswa Bimbingan
    </p>

    <!-- Card Daftar Mahasiswa Bimbingan -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Mahasiswa yang Anda Bimbing</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari mahasiswa...">
            </div>
            
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Email</th>
                            <th>Koordinator</th>
                            <th>Mentor Perusahaan</th>
                            <th>Perusahaan KP</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="tableBody" data-base-number="{{ $mahasiswaList->firstItem() ?? 0 }}">
                        @forelse($mahasiswaList as $index => $item)
                        @php
                            $penerimaanDiterima = $item->mahasiswa->penerimaanKP->first();
                            $namaPerusahaan = $penerimaanDiterima ? $penerimaanDiterima->nama_perusahaan : '-';
                        @endphp
                        <tr class="searchable-row" data-search="{{ strtolower(($item->mahasiswa->nim ?? '') . ' ' . $item->mahasiswa->name . ' ' . $item->mahasiswa->email . ' ' . $namaPerusahaan) }}">
                            <td class="row-number">{{ $mahasiswaList->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $item->mahasiswa->name }}</strong>
                            </td>
                            <td>{{ $item->mahasiswa->nim ?? '-' }}</td>
                            <td>{{ $item->mahasiswa->email }}</td>
                            <td>
                                @if($item->koordinator)
                                    <span class="badge bg-info">{{ $item->koordinator->name }}</span>
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
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
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada mahasiswa yang ditugaskan kepada Anda</td>
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
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush