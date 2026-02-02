@extends('layouts.app')

@section('content')
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

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Dosen & Koordinator</h5>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddStaff">
                <i class="bx bx-user-plus"></i> Tambah Akun
            </button>
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
                    <tbody id="tableBody" data-base-number="{{ $staffList->firstItem() ?? 0 }}">
                        @forelse($staffList as $index => $staff)
                            @php
                                $roles = $staff->roles ?? [];
                            @endphp
                            <tr class="searchable-row" data-search="{{ strtolower(($staff->name ?? '') . ' ' . ($staff->nidn ?? '') . ' ' . ($staff->email ?? '') . ' ' . implode(' ', $roles)) }}">
                                <td class="row-number">{{ $staffList->firstItem() + $index }}</td>
                                <td>{{ $staff->name ?? '-' }}</td>
                                <td>{{ $staff->nidn ?? '-' }}</td>
                                <td>{{ $staff->email ?? '-' }}</td>
                                <td>
                                    <form id="roleForm{{ $staff->id }}" method="POST" action="{{ route('admin.users.updateRole', $staff->id) }}" class="d-flex flex-wrap gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="roles[]" id="roleDosen{{ $staff->id }}"
                                                value="dosen" {{ $staff->hasRole('dosen') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="roleDosen{{ $staff->id }}">Dosen</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="roles[]" id="roleKoor{{ $staff->id }}"
                                                value="koor" {{ $staff->hasRole('koor') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="roleKoor{{ $staff->id }}">Koor</label>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditStaff{{ $staff->id }}">
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalEditStaff{{ $staff->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Data Staff</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.users.update', $staff->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $staff->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" name="email" class="form-control" value="{{ $staff->email }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">NIDN</label>
                                                    <input type="text" name="nidn" class="form-control" value="{{ $staff->nidn }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data dosen atau koordinator.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($staffList->hasPages())
            <div class="mt-3">
                {{ $staffList->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalAddStaff" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Dosen / Koordinator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="modal-body">
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
                                <label class="form-label">NIDN</label>
                                <input type="text" name="nidn" class="form-control" value="{{ old('nidn') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label d-block">Role</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="roles[]" id="roleDosen"
                                        value="dosen" {{ in_array('dosen', old('roles', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="roleDosen">Dosen</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="roles[]" id="roleKoor"
                                        value="koor" {{ in_array('koor', old('roles', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="roleKoor">Koor</label>
                                </div>
                                <div class="form-text">Pilih minimal satu role.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">
                            <i class="bx bx-user-plus"></i> Tambah Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-submit role form saat checkbox diubah
    document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const formId = 'roleForm' + this.id.match(/\d+$/)[0];
            document.getElementById(formId).submit();
        });
    });
</script>
@endpush
