@extends('layouts.app')

@section('title', 'Edit Penerimaan KP')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Penerimaan Kerja Praktek</h4>
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

                        <form action="{{ route('mahasiswa.penerimaan.update', $penerimaan->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="pengajuan_kp_id" class="form-label">Pengajuan KP yang Diterima</label>
                                <select class="form-select" id="pengajuan_kp_id" name="pengajuan_kp_id" required>
                                    <option value="">Pilih pengajuan KP yang sudah diterima</option>
                                    @foreach($pengajuanDiterima as $pengajuan)
                                        <option value="{{ $pengajuan->id }}" {{ $penerimaan->pengajuan_kp_id == $pengajuan->id ? 'selected' : '' }}>
                                            {{ $pengajuan->perusahaan_tujuan }} -
                                            {{ $pengajuan->tanggal_pengajuan->format('d M Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                                <input type="text" 
                                    class="form-control @error('nama_perusahaan') is-invalid @enderror" 
                                    id="nama_perusahaan" 
                                    name="nama_perusahaan" 
                                    value="{{ old('nama_perusahaan', $penerimaan->nama_perusahaan) }}"
                                    placeholder="Ketik nama perusahaan..."
                                    autocomplete="off"
                                    required>
                                <div id="perusahaan-suggestions" class="dropdown-menu" style="width: 100%; max-height: 200px; overflow-y: auto;"></div>
                                <small class="text-muted">Ketik untuk mencari atau masukkan nama perusahaan baru</small>
                                @error('nama_perusahaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai KP</label>
                                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                                        value="{{ $penerimaan->tanggal_mulai->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai KP</label>
                                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                                        value="{{ $penerimaan->tanggal_selesai->format('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="file_surat_penerimaan" class="form-label">Upload Surat Penerimaan KP
                                    (Opsional)</label>
                                <input class="form-control" type="file" id="file_surat_penerimaan"
                                    name="file_surat_penerimaan" accept=".pdf">
                                <div class="form-text">Biarkan kosong jika tidak ingin mengubah file. Format PDF, maksimal
                                    2MB</div>
                                @if($penerimaan->file_surat_penerimaan)
                                    <small>File saat ini: <a href="{{ Storage::url($penerimaan->file_surat_penerimaan) }}"
                                            target="_blank">{{ basename($penerimaan->file_surat_penerimaan) }}</a></small>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">Update Penerimaan KP</button>
                            <a href="{{ route('mahasiswa.penerimaan') }}" class="btn btn-secondary">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-fill nama perusahaan ketika memilih pengajuan KP
        document.getElementById('pengajuan_kp_id').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                // Extract nama perusahaan dari text option
                const perusahaanName = selectedOption.text.split(' - ')[0];
                document.getElementById('nama_perusahaan').value = perusahaanName;
            } else {
                document.getElementById('nama_perusahaan').value = '';
            }
        });

        // Validasi tanggal
        document.getElementById('tanggal_mulai').addEventListener('change', function () {
            const tanggalMulai = new Date(this.value);
            const tanggalSelesai = document.getElementById('tanggal_selesai');
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (tanggalMulai < today) {
                alert('Tanggal mulai harus hari ini atau setelahnya');
                this.value = '';
                return;
            }

            if (tanggalSelesai.value && new Date(tanggalSelesai.value) <= tanggalMulai) {
                tanggalSelesai.value = '';
            }
        });

        document.getElementById('tanggal_selesai').addEventListener('change', function () {
            const tanggalSelesai = new Date(this.value);
            const tanggalMulai = document.getElementById('tanggal_mulai');

            if (tanggalMulai.value && tanggalSelesai <= new Date(tanggalMulai.value)) {
                alert('Tanggal selesai harus setelah tanggal mulai');
                this.value = '';
            }
        });

        // Data perusahaan untuk autocomplete
        const perusahaanData = [
            @foreach($perusahaans as $perusahaan)
            { nama: '{{ $perusahaan->nama_perusahaan }}' },
            @endforeach
        ];

        const perusahaanInput = document.getElementById('nama_perusahaan');
        const suggestionDiv = document.getElementById('perusahaan-suggestions');

        perusahaanInput.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            suggestionDiv.innerHTML = '';
            
            if (value.length === 0) {
                suggestionDiv.classList.remove('show');
                return;
            }

            const filtered = perusahaanData.filter(p => 
                p.nama.toLowerCase().includes(value)
            );

            if (filtered.length > 0) {
                filtered.forEach(item => {
                    const div = document.createElement('a');
                    div.className = 'dropdown-item';
                    div.href = '#';
                    div.textContent = item.nama;
                    div.onclick = function(e) {
                        e.preventDefault();
                        perusahaanInput.value = item.nama;
                        suggestionDiv.classList.remove('show');
                    };
                    suggestionDiv.appendChild(div);
                });
                suggestionDiv.classList.add('show');
            } else {
                suggestionDiv.classList.remove('show');
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target !== perusahaanInput) {
                suggestionDiv.classList.remove('show');
            }
        });
    </script>
@endsection