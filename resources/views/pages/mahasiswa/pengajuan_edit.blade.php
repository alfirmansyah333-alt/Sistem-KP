@extends('layouts.app')

@section('title', 'Edit Pengajuan KP')

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Pengajuan Kerja Praktik</h4>
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

        <form action="{{ route('mahasiswa.pengajuan.update', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PATCH')

          <div class="mb-3">
            <label for="perusahaan_tujuan" class="form-label">Perusahaan Tujuan</label>
            <input type="text" 
                class="form-control" 
                id="perusahaan_tujuan" 
                name="perusahaan_tujuan" 
                value="{{ $pengajuan->perusahaan_tujuan }}"
                placeholder="Ketik nama perusahaan..."
                autocomplete="off"
                required>
            <div id="perusahaan-suggestions" class="dropdown-menu" style="width: 100%; max-height: 200px; overflow-y: auto;"></div>
            <small class="text-muted">Ketik untuk mencari atau masukkan nama perusahaan baru</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Mitra dengan Perusahaan</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="mitra_dengan_perusahaan" id="mitra_ya" value="iya" {{ $pengajuan->mitra_dengan_perusahaan == 'iya' ? 'checked' : '' }} required>
              <label class="form-check-label" for="mitra_ya">
                Ya
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="mitra_dengan_perusahaan" id="mitra_tidak" value="tidak" {{ $pengajuan->mitra_dengan_perusahaan == 'tidak' ? 'checked' : '' }} required>
              <label class="form-check-label" for="mitra_tidak">
                Tidak
              </label>
            </div>
            <small class="text-muted">Status mitra akan terisi otomatis jika perusahaan sudah terdaftar</small>
          </div>

          <div class="mb-3">
            <label for="file_surat_pengajuan" class="form-label">Upload Surat Pengajuan KP (Opsional)</label>
            <input type="file" class="form-control" id="file_surat_pengajuan" name="file_surat_pengajuan" accept=".pdf">
            <div class="form-text">Biarkan kosong jika tidak ingin mengubah file. Format PDF, maksimal 2MB</div>
            @if($pengajuan->file_surat_pengajuan)
              <small>File saat ini: <a href="{{ Storage::url($pengajuan->file_surat_pengajuan) }}" target="_blank">{{ basename($pengajuan->file_surat_pengajuan) }}</a></small>
            @endif
          </div>

          <button type="submit" class="btn btn-primary">Update Pengajuan</button>
          <a href="{{ route('mahasiswa.pengajuan') }}" class="btn btn-secondary">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
    const perusahaanData = [
        @foreach($perusahaans as $perusahaan)
        { nama: '{{ $perusahaan->nama_perusahaan }}', mitra: '{{ $perusahaan->is_mitra ? "iya" : "tidak" }}' },
        @endforeach
    ];

    const perusahaanInput = document.getElementById('perusahaan_tujuan');
    const suggestionDiv = document.getElementById('perusahaan-suggestions');
    const mitraYa = document.getElementById('mitra_ya');
    const mitraTidak = document.getElementById('mitra_tidak');

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
                    
                    if (item.mitra === 'iya') {
                        mitraYa.checked = true;
                    } else {
                        mitraTidak.checked = true;
                    }
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