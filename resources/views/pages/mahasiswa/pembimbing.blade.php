@extends('layouts.app')

@section('title', 'Data Pembimbing')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <p class="text-muted small mb-2">
            <span class="text-muted">Mahasiswa /</span> Data Pembimbing
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

        <!-- Card Data Pembimbing Saat Ini -->
        @if($pembimbingan)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Pembimbing Saat Ini</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">Koordinator KP :</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                {{ $pembimbingan->koordinator ? $pembimbingan->koordinator->name : '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">Dosen Pembimbing :</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                {{ $pembimbingan->dosenPembimbing ? $pembimbingan->dosenPembimbing->name : 'Belum ditentukan oleh koordinator' }}
                            </p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">Mentor Perusahaan :</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                {{ $pembimbingan->mentor_perusahaan ?? 'Belum diisi' }}
                            </p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">Perusahaan KP :</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext">
                                {{ $penerimaanDiterima ? $penerimaanDiterima->nama_perusahaan : '-' }}
                            </p>
                            @if(!$penerimaanDiterima)
                                <small class="text-muted">Belum ada penerimaan KP yang disetujui</small>
                            @endif
                        </div>
                    </div>
                    @if($pembimbingan->catatan)
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Catatan</label>
                            <div class="col-sm-9">
                                <p class="form-control-plaintext">{{ $pembimbingan->catatan }}</p>
                            </div>
                        </div>
                    @endif
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalEditPembimbing">
                            <i class='bx bx-edit-alt me-1'></i> Ubah data
                        </button>
                    </div>
                </div>
            </div>
        @else
            <!-- Form Pengisian Awal -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Pilih Koordinator KP</h5>
                </div>
                <div class="card-body">
                    @if($penerimaanDiterima)
                        <div class="alert alert-info mb-3">
                            <i class='bx bx-info-circle me-2'></i>
                            Perusahaan KP Anda: <strong>{{ $penerimaanDiterima->nama_perusahaan }}</strong>
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                            <i class='bx bx-error me-2'></i>
                            Anda belum memiliki penerimaan KP yang disetujui. Silakan ajukan penerimaan KP terlebih dahulu.
                        </div>
                    @endif

                    <form action="{{ route('mahasiswa.pembimbing.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="koordinator_id">Pilih Koordinator KP <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select @error('koordinator_id') is-invalid @enderror" id="koordinator_id"
                                    name="koordinator_id" required>
                                    <option value="">-- Pilih Koordinator --</option>
                                    @foreach($koordinatorList as $koordinator)
                                        <option value="{{ $koordinator->id }}" {{ old('koordinator_id') == $koordinator->id ? 'selected' : '' }}>
                                            {{ $koordinator->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('koordinator_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Koordinator akan menentukan dosen pembimbing Anda</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label" for="mentor_perusahaan">Mentor Perusahaan</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('mentor_perusahaan') is-invalid @enderror"
                                    id="mentor_perusahaan" name="mentor_perusahaan" value="{{ old('mentor_perusahaan') }}"
                                    placeholder="Nama mentor di perusahaan">
                                @error('mentor_perusahaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class='bx bx-save me-1'></i> Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Edit -->
    @if($pembimbingan)
        <div class="modal fade" id="modalEditPembimbing" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('mahasiswa.pembimbing.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Ubah data pembimbing</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="edit_koordinator_id">Koordinator KP <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="edit_koordinator_id" name="koordinator_id" required>
                                    @foreach($koordinatorList as $koordinator)
                                        <option value="{{ $koordinator->id }}" {{ $pembimbingan->koordinator_id == $koordinator->id ? 'selected' : '' }}>
                                            {{ $koordinator->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Koordinator akan menentukan dosen pembimbing Anda</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="modal_mentor_perusahaan">Mentor Perusahaan</label>
                                <input type="text" class="form-control" id="modal_mentor_perusahaan" name="mentor_perusahaan"
                                    value="{{ old('mentor_perusahaan', $pembimbingan->mentor_perusahaan) }}"
                                    placeholder="Nama mentor di perusahaan">
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
    @endif
@endsection