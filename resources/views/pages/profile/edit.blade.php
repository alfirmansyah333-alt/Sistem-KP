@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pengaturan /</span> Edit Profil</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Detail Profil</h5>
                <!-- Account -->
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

                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                        @else
                            <img src="{{ asset('assets/img/avatars/DefaultProfile.png') }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                        @endif
                        <div class="button-wrapper">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <form action="{{ route('profile.upload-photo') }}" method="POST" enctype="multipart/form-data" id="profilePhotoForm" style="margin: 0;">
                                    @csrf
                                    <label for="upload" class="btn btn-primary" tabindex="0" style="cursor: pointer;">
                                        <i class="bx bx-upload me-1"></i>
                                        <span>Upload Foto</span>
                                        <input type="file" id="upload" class="account-file-input" name="profile_photo" hidden accept="image/png, image/jpeg, image/jpg, image/gif" onchange="submitPhotoForm()" />
                                    </label>
                                </form>
                                @if($user->profile_photo_path)
                                    <form action="{{ route('profile.delete-photo') }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus foto profil?')">
                                            <i class="bx bx-trash me-1"></i>
                                            <span>Hapus Foto</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">JPG, GIF atau PNG. Maksimal 2MB</p>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus />
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email_username" class="form-label">Email</label>
                                <div class="input-group input-group-merge">
                                    <input type="text" id="email_username" name="email_username" class="form-control @error('email_username') is-invalid @enderror" placeholder="username" value="{{ old('email_username', explode('@', $user->email)[0]) }}" required />
                                    <span class="input-group-text">{{ '@' . explode('@', $user->email)[1] }}</span>
                                </div>
                                @error('email_username')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="no_telp">Nomor Telepon</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">+62</span>
                                    <input type="text" id="no_telp" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" placeholder="812 3456 7890" value="{{ old('no_telp', $user->no_telp) }}" />
                                </div>
                                @error('no_telp')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bx bx-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('profile.change-password') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-lock"></i> Ubah Password
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function submitPhotoForm() {
    document.getElementById('profilePhotoForm').submit();
}
</script>
@endsection
