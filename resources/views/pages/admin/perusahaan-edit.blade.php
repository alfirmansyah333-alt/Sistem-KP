@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Perusahaan</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.perusahaan.update', $perusahaan->id) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-3">
                <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                <input type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror" 
                    id="nama_perusahaan" name="nama_perusahaan" 
                    value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan) }}" required>
                @error('nama_perusahaan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="is_mitra" class="form-label">Status Mitra</label>
                <select class="form-select @error('is_mitra') is-invalid @enderror" id="is_mitra" name="is_mitra" required>
                    <option value="0" {{ old('is_mitra', $perusahaan->is_mitra) == 0 ? 'selected' : '' }}>Tidak</option>
                    <option value="1" {{ old('is_mitra', $perusahaan->is_mitra) == 1 ? 'selected' : '' }}>Ya</option>
                </select>
                @error('is_mitra')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.perusahaan.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
