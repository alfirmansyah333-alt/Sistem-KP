@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Tambah Akun Koordinator</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <input type="hidden" name="role" value="koor">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIDN (opsional)</label>
                        <input type="text" name="nidn" class="form-control" value="{{ old('nidn') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-primary">
                        <i class="bx bx-user-plus"></i> Tambah Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Koordinator</h5>
            <div style="width: 300px;">
                <div class="input-group">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama / NIDN / email...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle" id="tableData">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIDN</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" data-base-number="{{ $koorList->firstItem() ?? 0 }}">
                        @forelse($koorList as $index => $koor)
                            <tr class="searchable-row" data-search="{{ strtolower(($koor->name ?? '') . ' ' . ($koor->nidn ?? '') . ' ' . ($koor->email ?? '') . ' ' . implode(' ', $koor->roles ?? [])) }}">
                                <td class="row-number">{{ $koorList->firstItem() + $index }}</td>
                                <td>{{ $koor->name ?? '-' }}</td>
                                <td>{{ $koor->nidn ?? '-' }}</td>
                                <td>{{ $koor->email ?? '-' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.updateRole', $koor->id) }}" class="d-flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="form-select form-select-sm" style="min-width: 140px;">
                                            <option value="dosen" {{ $koor->hasRole('dosen') ? 'selected' : '' }}>Dosen</option>
                                            <option value="koor" {{ $koor->hasRole('koor') ? 'selected' : '' }}>Koor</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary">Simpan</button>
                                    </form>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">Aktif</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data koordinator.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($koorList->hasPages())
            <div class="mt-3">
                {{ $koorList->links() }}
            </div>
        @endif
    </div>
@endsection
