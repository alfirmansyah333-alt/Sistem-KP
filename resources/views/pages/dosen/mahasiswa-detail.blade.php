@extends('layouts.app')

@section('title', 'Data Pembimbing KP')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ==== DUMMY DATA (hapus nanti setelah backend) ==== --}}
        @php
            // Dummy: if null = form muncul
            $mentor = [
                'nama' => 'Budi Santoso',
                'jabatan' => 'Supervisor IT'
            ];

            $pembimbingKampus = [
                'nama' => 'Dr. Andi Wijaya',
                'nidn' => '123456789'
            ];
        @endphp

        {{-- ============================= --}}
        {{-- CARD 1: MENTOR PERUSAHAAN --}}
        {{-- ============================= --}}
        <div class="card mb-4">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>Mentor Perusahaan</span>

                @if($mentor)
                    <button type="button" class="btn btn-sm btn-warning rounded-pill" id="editMentorBtn">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </button>
                @endif
            </h5>

            <div class="card-body">

                {{-- Jika ADA DATA -> tampilkan data --}}
                @if ($mentor)
                    <div id="mentorView">
                        <p><strong>Nama:</strong> {{ $mentor['nama'] }}</p>
                        <p><strong>Jabatan:</strong> {{ $mentor['jabatan'] }}</p>
                    </div>
                @endif

                {{-- Form input --}}
                <form id="mentorForm" class="{{ $mentor ? 'd-none' : '' }}">
                    <div class="mb-3">
                        <label class="form-label">Nama Mentor Perusahaan</label>
                        <input type="text" class="form-control" value="{{ $mentor['nama'] ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" class="form-control" value="{{ $mentor['jabatan'] ?? '' }}">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary rounded-pill">Simpan</button>
                    </div>
                </form>

            </div>
        </div>


        {{-- ============================= --}}
        {{-- CARD 2: PEMBIMBING KAMPUS --}}
        {{-- ============================= --}}
        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>Pembimbing Kampus (PCR)</span>

                @if($pembimbingKampus)
                    <button type="button" class="btn btn-sm btn-warning rounded-pill" id="editKampusBtn">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </button>
                @endif
            </h5>

            <div class="card-body">

                {{-- Jika ADA DATA -> tampilkan data --}}
                @if ($pembimbingKampus)
                    <div id="kampusView">
                        <p><strong>Nama:</strong> {{ $pembimbingKampus['nama'] }}</p>
                        <p><strong>NIDN:</strong> {{ $pembimbingKampus['nidn'] }}</p>
                    </div>
                @endif

                {{-- Form input --}}
                <form id="kampusForm" class="{{ $pembimbingKampus ? 'd-none' : '' }}">
                    <div class="mb-3">
                        <label class="form-label">Nama Pembimbing Kampus</label>
                        <input type="text" class="form-control" value="{{ $pembimbingKampus['nama'] ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">NIDN</label>
                        <input type="text" class="form-control" value="{{ $pembimbingKampus['nidn'] ?? '' }}">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary rounded-pill">Simpan</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Toggle Edit Mentor
        document.getElementById('editMentorBtn')?.addEventListener('click', function () {
            document.getElementById('mentorView').classList.add('d-none');
            document.getElementById('mentorForm').classList.remove('d-none');
        });

        // Toggle Edit Pembimbing Kampus
        document.getElementById('editKampusBtn')?.addEventListener('click', function () {
            document.getElementById('kampusView').classList.add('d-none');
            document.getElementById('kampusForm').classList.remove('d-none');
        });
    </script>
@endpush