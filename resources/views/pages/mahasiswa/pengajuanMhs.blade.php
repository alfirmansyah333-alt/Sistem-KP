@extends('layouts.app')

@section('title', 'Pengajuan KP')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <p class="text-muted small mb-2">
            <span class="text-muted">Mahasiswa /</span> Pengajuan KP
        </p>

        <!-- Modal Alert Success -->
        <div class="modal fade" id="modalAlertSuccess" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-success">
                            <i class="bx bx-check-circle me-2"></i>Berhasil
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="successMessage" class="mb-0"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Alert Error -->
        <div class="modal fade" id="modalAlertError" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i class="bx bx-error-circle me-2"></i>Error
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="errorMessage" class="mb-0"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Penolakan -->
        <div class="modal fade" id="modalConfirmReject" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-warning">
                            <i class="bx bx-error-circle me-2"></i>Konfirmasi Penolakan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="bx bx-error-circle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h6 class="text-center mb-3">PERINGATAN</h6>
                        <p class="text-center mb-0">
                            Status <strong class="text-danger">DITOLAK</strong> tidak dapat diubah kembali.<br>
                            Apakah Anda yakin ingin mengubah status pengajuan ini menjadi ditolak?
                        </p>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-danger" id="btnConfirmReject">
                            <i class="bx bx-check me-1"></i>Ya, Ubah status
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i class="bx bx-trash me-2"></i>Konfirmasi Hapus
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="bx bx-trash text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h6 class="text-center mb-3">PERINGATAN</h6>
                        <p class="text-center mb-0">
                            Data pengajuan yang dihapus <strong class="text-danger">TIDAK DAPAT</strong> dikembalikan.<br>
                            Apakah Anda yakin ingin menghapus pengajuan ini?
                        </p>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                            <i class="bx bx-check me-1"></i>Ya, Hapus Pengajuan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Diterima -->
        <div class="modal fade" id="modalConfirmAccept" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-success">
                            <i class="bx bx-check-circle me-2"></i>Konfirmasi Diterima
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="bx bx-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h6 class="text-center mb-3">KONFIRMASI</h6>
                        <p class="text-center mb-0">
                            Apakah perusahaan sudah <strong class="text-success">MENYETUJUI</strong> pengajuan ini?<br>
                            Status akan diubah menjadi diterima dan tidak dapat diubah kembali.
                        </p>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>Batal
                        </button>
                        <button type="button" class="btn btn-success" id="btnConfirmAccept">
                            <i class="bx bx-check me-1"></i>Ya, Terima Pengajuan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Sistem Prioritas -->
        <div class="alert alert-info alert-dismissible" role="alert">
            <h6 class="alert-heading mb-2">
                <i class="bx bx-info-circle me-1"></i>Sistem Prioritas Pengajuan KP
            </h6>
            <ul class="mb-0">
                <li>Pengajuan di periode yang lebih baru memiliki prioritas lebih tinggi</li>
                <li>Jika ada pengajuan yang diterima di periode sebelumnya, Anda harus menunggu keputusan periode yang lebih
                    baru</li>
                <li>Jika periode yang lebih baru ditolak, Anda bisa mengambil yang sudah diterima di periode sebelumnya</li>
                <li>Jika periode yang lebih baru diterima, Anda wajib mengambilnya</li>
                <li><strong>Jika sudah ada pengajuan yang diterima, Anda tidak dapat melakukan pengajuan KP lagi</strong></li>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Card daftar pengajuan -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Pengajuan Kerja Praktek</h5>
                <div class="text-end">
                    <small>Jumlah pengajuan: {{ $jumlahPengajuan }}/3</small>
                </div>

                <!-- Tombol Ajukan KP -->
                <button type="button" class="btn btn-primary btn-sm rounded-pill {{ !$bisaAjukan ? 'disabled' : '' }}" 
                    data-bs-toggle="modal" data-bs-target="#modalPengajuan"
                    {{ !$bisaAjukan ? 'aria-disabled=true' : '' }}>
                    <i class="bx bx-upload me-1"></i> Ajukan KP
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Periode</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Perusahaan</th>
                            <th>Mitra</th>
                            <th>Dokumen</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pengajuanList as $index => $pengajuan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                                <td>{{ $pengajuan->perusahaan_tujuan }}</td>
                                <td>
                                    @if($pengajuan->mitra_dengan_perusahaan == 'iya')
                                        <span class="badge bg-label-success">Ya</span>
                                    @else
                                        <span class="badge bg-label-secondary">Tidak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pengajuan->file_surat_pengajuan)
                                        <a href="{{ Storage::url($pengajuan->file_surat_pengajuan) }}" target="_blank"
                                            class="text-primary">
                                            <i class="bx bx-file me-1"></i> Surat Pengajuan
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($pengajuan->status == 'menunggu')
                                        @if(in_array($pengajuan->id, $editablePengajuan))
                                            <!-- Dropdown ubah status -->
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-warning dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bx bx-time"></i> Menunggu
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form id="formAccept{{ $pengajuan->id }}"
                                                            action="{{ route('mahasiswa.pengajuan.updateStatus', $pengajuan->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="diterima">
                                                            <button type="button" class="dropdown-item text-success"
                                                                onclick="openAcceptModal({{ $pengajuan->id }})">
                                                                <i class="bx bx-check-circle me-1"></i> Diterima
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form id="formReject{{ $pengajuan->id }}"
                                                            action="{{ route('mahasiswa.pengajuan.updateStatus', $pengajuan->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="ditolak">
                                                            <button type="button" class="dropdown-item text-danger"
                                                                onclick="openRejectModal({{ $pengajuan->id }})">
                                                                <i class="bx bx-x-circle me-1"></i> Ditolak
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        @else
                                            @if($sudahAdaDiterima)
                                                <span class="badge bg-label-secondary"
                                                    title="Tidak dapat diubah karena sudah ada pengajuan yang diterima">
                                                    Tertutup
                                                </span>
                                            @else
                                                <span class="badge bg-label-warning"
                                                    title="Menunggu keputusan dari pengajuan periode yang lebih baru">
                                                    <i class="bx bx-time me-1"></i>Menunggu Prioritas
                                                </span>
                                            @endif
                                        @endif
                                    @elseif($pengajuan->status == 'diterima')
                                        <span class="badge bg-label-success">Diterima</span>
                                    @else
                                        <span class="badge bg-label-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($pengajuan->status == 'menunggu')
                                        @if(!$sudahAdaDiterima)
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ route('mahasiswa.pengajuan.edit', $pengajuan->id) }}" class="dropdown-item">
                                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                                    </a>
                                                    <form id="formDelete{{ $pengajuan->id }}"
                                                        action="{{ route('mahasiswa.pengajuan.destroy', $pengajuan->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item text-danger"
                                                            onclick="openDeleteModal({{ $pengajuan->id }})">
                                                            <i class="bx bx-trash me-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" disabled>
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" disabled>
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @for($i = $jumlahPengajuan; $i < 3; $i++)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td><span class="badge bg-label-secondary">Belum Mengajukan</span></td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Ajukan KP -->
        <div class="modal fade" id="modalPengajuan" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Ajukan Kerja Praktek</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form id="pengajuanForm" action="{{ route('mahasiswa.pengajuan.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="perusahaan_tujuan" class="form-label">Perusahaan Tujuan</label>
                                    <input type="text" 
                                        id="perusahaan_tujuan" 
                                        class="form-control" 
                                        name="perusahaan_tujuan"
                                        placeholder="Ketik nama perusahaan..."
                                        autocomplete="off"
                                        required>
                                    <div id="perusahaan-suggestions" class="dropdown-menu" style="width: 100%; max-height: 200px; overflow-y: auto;"></div>
                                    <small class="text-muted">Ketik untuk mencari atau masukkan nama perusahaan baru</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label">Mitra dengan Perusahaan</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mitra_dengan_perusahaan"
                                            id="mitra_ya" value="iya" required>
                                        <label class="form-check-label" for="mitra_ya">
                                            Ya
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="mitra_dengan_perusahaan"
                                            id="mitra_tidak" value="tidak" required>
                                        <label class="form-check-label" for="mitra_tidak">
                                            Tidak
                                        </label>
                                    </div>
                                    <small class="text-muted">Status mitra akan terisi otomatis jika perusahaan sudah terdaftar</small>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col mb-0">
                                    <label for="file_surat_pengajuan" class="form-label">Upload Surat Pengajuan KP</label>
                                    <input class="form-control" type="file" id="file_surat_pengajuan"
                                        name="file_surat_pengajuan" accept=".pdf" required>
                                    <div class="form-text">Format PDF, maksimal 2MB</div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" form="pengajuanForm">Kirim Pengajuan</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        let currentRejectId = null;
        let currentDeleteId = null;
        let currentAcceptId = null;

        function openRejectModal(pengajuanId) {
            currentRejectId = pengajuanId;
            var rejectModal = new bootstrap.Modal(document.getElementById('modalConfirmReject'));
            rejectModal.show();
        }

        function openDeleteModal(pengajuanId) {
            currentDeleteId = pengajuanId;
            var deleteModal = new bootstrap.Modal(document.getElementById('modalConfirmDelete'));
            deleteModal.show();
        }

        function openAcceptModal(pengajuanId) {
            currentAcceptId = pengajuanId;
            var acceptModal = new bootstrap.Modal(document.getElementById('modalConfirmAccept'));
            acceptModal.show();
        }

        document.getElementById('btnConfirmReject').addEventListener('click', function () {
            if (currentRejectId) {
                document.getElementById('formReject' + currentRejectId).submit();
            }
        });

        document.getElementById('btnConfirmDelete').addEventListener('click', function () {
            if (currentDeleteId) {
                document.getElementById('formDelete' + currentDeleteId).submit();
            }
        });

        document.getElementById('btnConfirmAccept').addEventListener('click', function () {
            if (currentAcceptId) {
                document.getElementById('formAccept' + currentAcceptId).submit();
            }
        });

        @if(session('success'))
            document.getElementById('successMessage').textContent = "{{ session('success') }}";
            var successModal = new bootstrap.Modal(document.getElementById('modalAlertSuccess'));
            successModal.show();
        @endif

        @if(session('error'))
            document.getElementById('errorMessage').textContent = "{{ session('error') }}";
            var errorModal = new bootstrap.Modal(document.getElementById('modalAlertError'));
            errorModal.show();
        @endif

        // Data perusahaan untuk autocomplete
        const perusahaanData = [
            @foreach($perusahaans as $perusahaan)
            { nama: '{{ $perusahaan->nama_perusahaan }}', mitra: '{{ $perusahaan->is_mitra ? "iya" : "tidak" }}' },
            @endforeach
        ];

        const perusahaanInput = document.getElementById('perusahaan_tujuan');
        const suggestionDiv = document.getElementById('perusahaan-suggestions');
        const mitraYa = document.getElementById('mitra_ya');
        const mitraTidak = document.getElementById('mitra_tidak');

        // Function untuk setup autocomplete
        function setupAutocomplete() {
            // Reset suggestions saat modal dibuka
            suggestionDiv.innerHTML = '';
            suggestionDiv.classList.remove('show');
            
            // Show autocomplete on input
            perusahaanInput.addEventListener('input', handleInput);
        }

        function handleInput() {
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
                        
                        // Auto-fill mitra status
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
        }

        // Setup autocomplete saat page load
        setupAutocomplete();

        // Re-setup autocomplete saat modal dibuka
        const modalPengajuan = document.getElementById('modalPengajuan');
        modalPengajuan.addEventListener('shown.bs.modal', function() {
            // Reset form
            document.getElementById('pengajuanForm').reset();
            suggestionDiv.innerHTML = '';
            suggestionDiv.classList.remove('show');
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target !== perusahaanInput) {
                suggestionDiv.classList.remove('show');
            }
        });
    </script>
@endsection