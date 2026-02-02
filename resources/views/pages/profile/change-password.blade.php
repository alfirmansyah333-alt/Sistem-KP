@extends('layouts.app')

@section('title', 'Ubah Password')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <!-- Ubah Password Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ubah Password</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <!-- Password Saat Ini -->
                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" placeholder="Masukkan password saat ini" required>
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('current_password')">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            @error('current_password')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password Baru -->
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="Masukkan password baru" required>
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('password')">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Password harus terdiri dari minimal 8 karakter dan berisi kombinasi huruf, angka, dan karakter khusus.
                            </small>
                            @error('password')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" placeholder="Konfirmasi password baru" required>
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('password_confirmation')">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            @error('password_confirmation')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Simpan Password Baru
                            </button>
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = event.target.closest('span').querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    } else {
        field.type = 'password';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    }
}
</script>
@endsection
