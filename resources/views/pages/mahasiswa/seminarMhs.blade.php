@extends('layouts.app')

@section('title', 'Pendaftaran Seminar KP')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <p class="text-muted small mb-2">
            <span class="text-muted">Mahasiswa /</span> Seminar KP
        </p>
        
        {{-- Modal Pendaftaran Seminar --}}
        <div class="modal fade" id="modalDaftarSeminar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('mahasiswa.seminar.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bx bx-book me-2"></i>Daftar Seminar KP
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            {{-- Judul KP --}}
                            <div class="mb-3">
                                <label for="judulKP" class="form-label">Judul KP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul_kp') is-invalid @enderror" 
                                    id="judulKP" name="judul_kp"
                                    placeholder="Masukkan judul KP final atau sementara" 
                                    value="{{ old('judul_kp') }}" required>
                                @error('judul_kp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tanggal Seminar --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggalSeminar" class="form-label">Tanggal Seminar <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_seminar') is-invalid @enderror" 
                                        id="tanggalSeminar" name="tanggal_seminar"
                                        value="{{ old('tanggal_seminar') }}" required>
                                    @error('tanggal_seminar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="jamSeminar" class="form-label">Jam Seminar <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control @error('jam_seminar') is-invalid @enderror" 
                                        id="jamSeminar" name="jam_seminar"
                                        value="{{ old('jam_seminar') }}" required>
                                    @error('jam_seminar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Ruangan --}}
                            <div class="mb-3">
                                <label for="ruanganSeminar" class="form-label">Ruangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ruangan') is-invalid @enderror" 
                                    id="ruanganSeminar" name="ruangan"
                                    placeholder="Contoh: R.150" 
                                    value="{{ old('ruangan') }}" required>
                                @error('ruangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tentukan jadwal sesuai kesepakatan dengan dosen pembimbing.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-send me-1"></i> Daftarkan Seminar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Daftar Pendaftaran Seminar --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Riwayat Pendaftaran Seminar KP</h5>
                <button type="button" class="btn btn-primary rounded-pill {{ !$bisaDaftar ? 'disabled' : '' }}" 
                    data-bs-toggle="modal" data-bs-target="#modalDaftarSeminar"
                    {{ !$bisaDaftar ? 'aria-disabled=true' : '' }}>
                    <i class="bx bx-plus me-1"></i> Daftar Seminar
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Judul KP</th>
                                <th>Tanggal Daftar</th>
                                <th>Jadwal Seminar</th>
                                <th>Jam</th>
                                <th>Ruangan</th>
                                <th>Status</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($seminarList as $index => $seminar)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
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
                                    <td>{{ $seminar->catatan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-file-blank fs-1 d-block mb-2"></i>
                                            Belum ada pendaftaran seminar KP
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
@endsection