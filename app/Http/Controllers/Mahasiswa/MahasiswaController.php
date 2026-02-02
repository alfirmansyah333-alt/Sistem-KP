<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKP;
use App\Models\PenerimaanKP;
use App\Models\SeminarKP;
use App\Models\PembimbinganKP;
use App\Models\LaporanKP;
use App\Models\LaporanKPLog;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        return view('pages.mahasiswa.dashboard');
    }

    public function pengajuan()
    {
        $user = auth()->user();
        $pengajuanList = $user->pengajuanKP()->orderBy('created_at', 'asc')->get();
        $jumlahPengajuan = $pengajuanList->count();
        $sudahAdaDiterima = $pengajuanList->contains('status', 'diterima');
        
        // Tidak bisa ajukan jika sudah mencapai batas 3 atau sudah ada yang diterima
        $bisaAjukan = $jumlahPengajuan < 3 && !$sudahAdaDiterima;

        // Tentukan pengajuan mana yang bisa diubah statusnya berdasarkan prioritas
        $editablePengajuan = $this->getEditablePengajuan($pengajuanList);

        // Get all perusahaan untuk autocomplete
        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

        return view('pages.mahasiswa.pengajuanMhs', compact('pengajuanList', 'bisaAjukan', 'jumlahPengajuan', 'editablePengajuan', 'sudahAdaDiterima', 'perusahaans'));
    }

    public function storePengajuan(Request $request)
    {
        $request->validate([
            'perusahaan_tujuan' => 'required|string|max:255',
            'mitra_dengan_perusahaan' => 'required|in:iya,tidak',
            'file_surat_pengajuan' => 'required|file|mimes:pdf|max:2048', // Max 2MB
        ]);

        $user = auth()->user();
        $jumlahPengajuan = $user->pengajuanKP()->count();
        $sudahAdaDiterima = $user->pengajuanKP()->where('status', 'diterima')->exists();

        if ($sudahAdaDiterima) {
            return back()->withErrors(['error' => 'Anda sudah memiliki pengajuan KP yang diterima. Tidak dapat mengajukan lagi.']);
        }

        if ($jumlahPengajuan >= 3) {
            return back()->withErrors(['error' => 'Anda sudah mencapai batas maksimal 3 kali pengajuan KP.']);
        }

        $filePath = $request->file('file_surat_pengajuan')->store('pengajuan_kp', 'public');

        // Create atau update perusahaan di master table
        $isMitra = $request->mitra_dengan_perusahaan === 'iya';
        Perusahaan::updateOrCreate(
            ['nama_perusahaan' => $request->perusahaan_tujuan],
            ['is_mitra' => $isMitra]
        );

        PengajuanKP::create([
            'user_id' => $user->id,
            'perusahaan_tujuan' => $request->perusahaan_tujuan,
            'mitra_dengan_perusahaan' => $request->mitra_dengan_perusahaan,
            'file_surat_pengajuan' => $filePath,
            'status' => 'menunggu',
            'tanggal_pengajuan' => now()->toDateString(),
            'periode' => $jumlahPengajuan + 1,
        ]);

        return back()->with('success', 'Surat pengajuan KP berhasil diunggah. Status: Menunggu persetujuan perusahaan.');
    }

    public function editPengajuan($id)
    {
        $pengajuan = PengajuanKP::findOrFail($id);

        if ($pengajuan->user_id !== auth()->id() || $pengajuan->status !== 'menunggu') {
            abort(403);
        }

        // Jika sudah ada pengajuan yang diterima, tidak bisa edit
        $sudahAdaDiterima = auth()->user()->pengajuanKP()->where('status', 'diterima')->exists();
        if ($sudahAdaDiterima) {
            abort(403, 'Tidak dapat mengedit pengajuan karena sudah ada pengajuan yang diterima.');
        }

        // Get all perusahaan untuk autocomplete
        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

        return view('pages.mahasiswa.pengajuan_edit', compact('pengajuan', 'perusahaans'));
    }

    public function updatePengajuan(Request $request, $id)
    {
        $pengajuan = PengajuanKP::findOrFail($id);

        if ($pengajuan->user_id !== auth()->id() || $pengajuan->status !== 'menunggu') {
            abort(403);
        }

        $request->validate([
            'perusahaan_tujuan' => 'required|string|max:255',
            'mitra_dengan_perusahaan' => 'required|in:iya,tidak',
            'file_surat_pengajuan' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // Create atau update perusahaan di master table
        $isMitra = $request->mitra_dengan_perusahaan === 'iya';
        Perusahaan::updateOrCreate(
            ['nama_perusahaan' => $request->perusahaan_tujuan],
            ['is_mitra' => $isMitra]
        );

        $data = [
            'perusahaan_tujuan' => $request->perusahaan_tujuan,
            'mitra_dengan_perusahaan' => $request->mitra_dengan_perusahaan,
        ];

        if ($request->hasFile('file_surat_pengajuan')) {
            // Delete old file
            if ($pengajuan->file_surat_pengajuan) {
                Storage::disk('public')->delete($pengajuan->file_surat_pengajuan);
            }
            $data['file_surat_pengajuan'] = $request->file('file_surat_pengajuan')->store('pengajuan_kp', 'public');
        }

        $pengajuan->update($data);

        return redirect()->route('mahasiswa.pengajuan')->with('success', 'Pengajuan KP berhasil diperbarui.');
    }

    public function destroyPengajuan($id)
    {
        $pengajuan = PengajuanKP::findOrFail($id);

        if ($pengajuan->user_id !== auth()->id() || $pengajuan->status !== 'menunggu') {
            abort(403);
        }

        // Jika sudah ada pengajuan yang diterima, tidak bisa hapus
        $sudahAdaDiterima = auth()->user()->pengajuanKP()->where('status', 'diterima')->exists();
        if ($sudahAdaDiterima) {
            return back()->with('error', 'Tidak dapat menghapus pengajuan karena sudah ada pengajuan yang diterima.');
        }

        // Delete file
        if ($pengajuan->file_surat_pengajuan) {
            Storage::disk('public')->delete($pengajuan->file_surat_pengajuan);
        }

        $pengajuan->delete();

        return back()->with('success', 'Pengajuan KP berhasil dihapus.');
    }

    public function updateStatusPengajuan(Request $request, $id)
    {
        $pengajuan = PengajuanKP::findOrFail($id);

        if ($pengajuan->user_id !== auth()->id()) {
            abort(403);
        }

        // Jika sudah ada pengajuan yang diterima, tidak bisa ubah status lagi
        $sudahAdaDiterima = auth()->user()->pengajuanKP()->where('status', 'diterima')->exists();
        if ($sudahAdaDiterima) {
            return back()->with('error', 'Tidak dapat mengubah status karena sudah ada pengajuan yang diterima.');
        }

        $request->validate([
            'status' => 'required|in:diterima,ditolak',
        ]);

        $user = auth()->user();
        $allPengajuan = $user->pengajuanKP()->orderBy('created_at', 'asc')->get();

        // Jika mengubah menjadi diterima, evaluasi prioritas
        if ($request->status === 'diterima') {
            $canAccept = $this->canAcceptPengajuan($pengajuan, $allPengajuan);
            if (!$canAccept) {
                return back()->with('error', 'Tidak dapat menerima pengajuan ini karena ada pengajuan di periode yang lebih baru yang masih menunggu keputusan.');
            }
            $rejectedCount = $this->handleAcceptancePriority($pengajuan, $allPengajuan);
        } 
        // Jika mengubah menjadi ditolak, evaluasi apakah bisa mengambil yang sudah diterima
        elseif ($request->status === 'ditolak') {
            $this->handleRejectionPriority($pengajuan, $allPengajuan);
        }

        $pengajuan->refresh(); // Refresh data setelah perubahan

        $message = $pengajuan->status === 'diterima' 
            ? 'Status pengajuan diubah menjadi Diterima. Silakan lanjut ke halaman Penerimaan KP.' 
            : 'Status pengajuan diubah menjadi Ditolak.';

        // Tambahkan informasi jika ada pengajuan lain yang otomatis ditolak
        if (isset($rejectedCount) && $rejectedCount > 0) {
            $message .= " {$rejectedCount} pengajuan periode sebelumnya otomatis ditolak karena prioritas.";
        }

        return back()->with('success', $message);
    }

    /**
     * Cek apakah pengajuan bisa diterima berdasarkan prioritas
     */
    private function canAcceptPengajuan($pengajuan, $allPengajuan)
    {
        $acceptedIndex = $allPengajuan->search(function ($item) use ($pengajuan) {
            return $item->id === $pengajuan->id;
        });

        // Jika ada pengajuan di periode yang lebih baru yang masih menunggu, tidak bisa terima
        for ($i = $acceptedIndex + 1; $i < $allPengajuan->count(); $i++) {
            if ($allPengajuan[$i]->status === 'menunggu') {
                return false;
            }
        }

        return true;
    }

    /**
     * Handle logika prioritas ketika pengajuan diterima
     */
    private function handleAcceptancePriority($acceptedPengajuan, $allPengajuan)
    {
        $acceptedIndex = $allPengajuan->search(function ($item) use ($acceptedPengajuan) {
            return $item->id === $acceptedPengajuan->id;
        });

        // Set status diterima (sudah dicek di canAcceptPengajuan bahwa tidak ada yang lebih baru menunggu)
        $acceptedPengajuan->update(['status' => 'diterima']);

        // Jika ada pengajuan yang lebih lama yang sudah diterima, tolak secara otomatis
        $rejectedCount = 0;
        for ($i = 0; $i < $acceptedIndex; $i++) {
            if ($allPengajuan[$i]->status === 'diterima') {
                // Update langsung berdasarkan ID untuk memastikan perubahan tersimpan
                PengajuanKP::where('id', $allPengajuan[$i]->id)->update(['status' => 'ditolak']);
                $rejectedCount++;
            }
        }

        return $rejectedCount; // Return jumlah yang ditolak untuk feedback
    }

    /**
     * Handle logika prioritas ketika pengajuan ditolak
     */
    private function handleRejectionPriority($rejectedPengajuan, $allPengajuan)
    {
        $rejectedIndex = $allPengajuan->search(function ($item) use ($rejectedPengajuan) {
            return $item->id === $rejectedPengajuan->id;
        });

        // Set status ditolak
        $rejectedPengajuan->update(['status' => 'ditolak']);

        // Jika pengajuan yang ditolak adalah yang terbaru, cek apakah ada yang sudah diterima di periode sebelumnya
        $hasNewerWaiting = false;
        for ($i = $rejectedIndex + 1; $i < $allPengajuan->count(); $i++) {
            if ($allPengajuan[$i]->status === 'menunggu') {
                $hasNewerWaiting = true;
                break;
            }
        }

        // Jika tidak ada yang lebih baru menunggu, dan ada yang sudah diterima di periode sebelumnya, biarkan saja
        // Logika ini sudah tercover di handleAcceptancePriority
    }

    /**
     * Tentukan pengajuan mana yang bisa diubah statusnya berdasarkan prioritas
     */
    private function getEditablePengajuan($pengajuanList)
    {
        // Jika sudah ada pengajuan yang diterima, tidak ada yang bisa diubah lagi
        $sudahAdaDiterima = $pengajuanList->contains('status', 'diterima');
        if ($sudahAdaDiterima) {
            return [];
        }

        $editable = [];

        foreach ($pengajuanList as $index => $pengajuan) {
            if ($pengajuan->status !== 'menunggu') {
                continue; // Skip yang sudah final
            }

            // Cek apakah ada pengajuan di periode yang lebih baru yang masih menunggu
            $hasNewerWaiting = false;
            for ($i = $index + 1; $i < $pengajuanList->count(); $i++) {
                if ($pengajuanList[$i]->status === 'menunggu') {
                    $hasNewerWaiting = true;
                    break;
                }
            }

            // Jika ada yang lebih baru menunggu, maka pengajuan ini tidak bisa diubah
            if (!$hasNewerWaiting) {
                $editable[] = $pengajuan->id;
            }
        }

        return $editable;
    }

    public function penerimaan()
    {
        $user = auth()->user();
        $penerimaanList = $user->penerimaanKP()->orderBy('created_at', 'desc')->get();

        return view('pages.mahasiswa.penerimaanMhs', compact('penerimaanList'));
    }

    public function createPenerimaan()
    {
        $user = auth()->user();
        
        // Cek apakah sudah ada penerimaan yang diterima
        $hasDiterima = $user->penerimaanKP()->where('status', 'diterima')->exists();
        if ($hasDiterima) {
            return redirect()->route('mahasiswa.penerimaan')->with('error', 'Anda sudah memiliki penerimaan KP yang diterima. Tidak dapat mengajukan penerimaan baru.');
        }
        
        $pengajuanDiterima = $user->pengajuanKP()->where('status', 'diterima')->get();
        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

        if ($pengajuanDiterima->isEmpty()) {
            return redirect()->route('mahasiswa.penerimaan')->with('error', 'Tidak ada pengajuan KP yang diterima.');
        }

        return view('pages.mahasiswa.penerimaan_create', compact('pengajuanDiterima', 'perusahaans'));
    }

    public function storePenerimaan(Request $request)
    {
        $request->validate([
            'pengajuan_kp_id' => 'required|exists:pengajuan_kp,id',
            'nama_perusahaan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date|after:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'file_surat_penerimaan' => 'required|file|mimes:pdf|max:2048',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        
        // Cek apakah sudah ada penerimaan yang diterima
        $hasDiterima = $user->penerimaanKP()->where('status', 'diterima')->exists();
        if ($hasDiterima) {
            return redirect()->route('mahasiswa.penerimaan')->with('error', 'Anda sudah memiliki penerimaan KP yang diterima. Tidak dapat mengajukan penerimaan baru.');
        }

        // Cek apakah pengajuan KP milik user dan statusnya diterima
        $pengajuan = PengajuanKP::where('id', $request->pengajuan_kp_id)
            ->where('user_id', $user->id)
            ->where('status', 'diterima')
            ->first();

        if (!$pengajuan) {
            return back()->withErrors(['error' => 'Pengajuan KP tidak valid atau belum diterima.']);
        }

        // Cek apakah sudah ada penerimaan yang sedang menunggu atau diterima untuk pengajuan ini
        $existingPenerimaan = PenerimaanKP::where('pengajuan_kp_id', $request->pengajuan_kp_id)
            ->whereIn('status', ['menunggu', 'diterima'])
            ->exists();
        if ($existingPenerimaan) {
            return back()->withErrors(['error' => 'Penerimaan KP untuk pengajuan ini sudah ada dan sedang menunggu persetujuan atau sudah diterima.']);
        }

        // Cari atau buat perusahaan di master jika belum ada
        $perusahaan = Perusahaan::firstOrCreate(
            ['nama_perusahaan' => $request->nama_perusahaan],
            ['is_mitra' => false] // Default tidak mitra untuk perusahaan baru
        );

        $filePath = $request->file('file_surat_penerimaan')->store('penerimaan_kp', 'public');

        PenerimaanKP::create([
            'user_id' => $user->id,
            'pengajuan_kp_id' => $request->pengajuan_kp_id,
            'perusahaan_id' => $perusahaan->id,
            'nama_perusahaan' => $request->nama_perusahaan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'file_surat_penerimaan' => $filePath,
            'status' => 'menunggu',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('mahasiswa.penerimaan')->with('success', 'Penerimaan KP berhasil diajukan dan menunggu persetujuan.');
    }

    public function editPenerimaan($id)
    {
        $penerimaan = PenerimaanKP::findOrFail($id);

        // Allow edit untuk status menunggu atau ditolak
        if ($penerimaan->user_id !== auth()->id() || !in_array($penerimaan->status, ['menunggu', 'ditolak'])) {
            abort(403);
        }

        $user = auth()->user();
        $pengajuanDiterima = $user->pengajuanKP()->where('status', 'diterima')->get();
        $perusahaans = Perusahaan::orderBy('nama_perusahaan')->get();

        return view('pages.mahasiswa.penerimaan_edit', compact('penerimaan', 'pengajuanDiterima', 'perusahaans'));
    }

    public function updatePenerimaan(Request $request, $id)
    {
        $penerimaan = PenerimaanKP::findOrFail($id);

        // Allow update untuk status menunggu atau ditolak
        if ($penerimaan->user_id !== auth()->id() || !in_array($penerimaan->status, ['menunggu', 'ditolak'])) {
            abort(403);
        }

        $request->validate([
            'pengajuan_kp_id' => 'required|exists:pengajuan_kp,id',
            'nama_perusahaan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date|after:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'file_surat_penerimaan' => 'nullable|file|mimes:pdf|max:2048',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        // Cek apakah pengajuan KP milik user dan statusnya diterima
        $pengajuan = PengajuanKP::where('id', $request->pengajuan_kp_id)
            ->where('user_id', $user->id)
            ->where('status', 'diterima')
            ->first();

        if (!$pengajuan) {
            return back()->withErrors(['error' => 'Pengajuan KP tidak valid atau belum diterima.']);
        }

        $data = [
            'pengajuan_kp_id' => $request->pengajuan_kp_id,
            'nama_perusahaan' => $request->nama_perusahaan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'catatan' => $request->catatan,
        ];

        // Jika status sebelumnya ditolak, reset ke menunggu
        if ($penerimaan->status === 'ditolak') {
            $data['status'] = 'menunggu';
        }

        // Cari atau buat perusahaan di master jika belum ada
        $perusahaan = Perusahaan::firstOrCreate(
            ['nama_perusahaan' => $request->nama_perusahaan],
            ['is_mitra' => false] // Default tidak mitra untuk perusahaan baru
        );
        $data['perusahaan_id'] = $perusahaan->id;

        if ($request->hasFile('file_surat_penerimaan')) {
            // Delete old file
            if ($penerimaan->file_surat_penerimaan) {
                Storage::disk('public')->delete($penerimaan->file_surat_penerimaan);
            }
            $data['file_surat_penerimaan'] = $request->file('file_surat_penerimaan')->store('penerimaan_kp', 'public');
        }

        $penerimaan->update($data);

        return redirect()->route('mahasiswa.penerimaan')->with('success', 'Penerimaan KP berhasil diperbarui.');
    }

    public function destroyPenerimaan($id)
    {
        $penerimaan = PenerimaanKP::findOrFail($id);

        if ($penerimaan->user_id !== auth()->id() || $penerimaan->status !== 'menunggu') {
            abort(403);
        }

        // Delete file
        if ($penerimaan->file_surat_penerimaan) {
            Storage::disk('public')->delete($penerimaan->file_surat_penerimaan);
        }

        $penerimaan->delete();

        return back()->with('success', 'Penerimaan KP berhasil dihapus.');
    }

    // Seminar KP (Mahasiswa)
    public function seminar()
    {
        $user = auth()->user();
        $seminarList = $user->seminarKP()->orderBy('created_at', 'desc')->get();
        
        // Cek apakah sudah ada seminar yang disetujui
        $sudahAdaDisetujui = $seminarList->contains('status', 'disetujui');
        
        // Mahasiswa hanya bisa daftar 1 seminar (tidak ada yang disetujui)
        $bisaDaftar = !$sudahAdaDisetujui;

        return view('pages.mahasiswa.seminarMhs', compact('seminarList', 'bisaDaftar'));
    }

    public function storeSeminar(Request $request)
    {
        $request->validate([
            'judul_kp' => 'required|string|max:255',
            'tanggal_seminar' => 'required|date',
            'jam_seminar' => 'required|date_format:H:i',
            'ruangan' => 'required|string|max:100',
        ]);

        $user = auth()->user();
        
        // Cek apakah sudah pernah disetujui
        $sudahAdaDisetujui = $user->seminarKP()->where('status', 'disetujui')->exists();
        if ($sudahAdaDisetujui) {
            return back()->withErrors(['error' => 'Anda sudah memiliki pendaftaran seminar yang disetujui.']);
        }

        SeminarKP::create([
            'user_id' => $user->id,
            'judul_kp' => $request->judul_kp,
            'tanggal_seminar' => $request->tanggal_seminar,
            'jam_seminar' => $request->jam_seminar,
            'ruangan' => $request->ruangan,
            'status' => 'menunggu',
        ]);

        return back()->with('success', 'Pendaftaran seminar KP berhasil dikirim. Status: Menunggu persetujuan koordinator.');
    }

    // Data Pembimbing (Mahasiswa)
    public function pembimbing()
    {
        $user = auth()->user();
        
        // Ambil data pembimbingan mahasiswa
        $pembimbingan = PembimbinganKP::where('user_id', $user->id)
            ->with(['koordinator', 'dosenPembimbing'])
            ->first();
        
        // Ambil penerimaan KP yang diterima untuk mendapatkan nama perusahaan
        $penerimaanDiterima = PenerimaanKP::where('user_id', $user->id)
            ->where('status', 'diterima')
            ->first();
        
        // Daftar koordinator untuk dropdown
        $koordinatorList = User::whereJsonContains('roles', 'koor')
            ->orWhereJsonContains('roles', 'koordinator')
            ->get();
        
        return view('pages.mahasiswa.pembimbing', compact('pembimbingan', 'koordinatorList', 'penerimaanDiterima'));
    }

    public function storePembimbing(Request $request)
    {
        $request->validate([
            'koordinator_id' => 'required|exists:users,id',
                    'mentor_perusahaan' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        // Update atau create data pembimbingan
        $data = ['koordinator_id' => $request->koordinator_id];
        
        if ($request->filled('mentor_perusahaan')) {
            $data['mentor_perusahaan'] = $request->mentor_perusahaan;
        }
        
        PembimbinganKP::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return back()->with('success', 'Data pembimbing berhasil disimpan.');
    }

    // Laporan KP (Mahasiswa)
    public function laporan()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        // Cek apakah sudah seminar KP dan disetujui
        $seminarDisetujui = $user->seminarKP()->where('status', 'disetujui')->exists();
        
        // Ambil laporan terbaru
        $laporan = $user->laporanKP()->latest()->first();
        
        // Load logs jika ada laporan
        $logs = [];
        if ($laporan) {
            $logs = LaporanKPLog::where('laporan_kp_id', $laporan->id)
                ->with('dosen')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('pages.mahasiswa.laporanMhs', compact('laporan', 'logs', 'seminarDisetujui'));
    }

    public function storeLaporan(Request $request)
    {
        // Validasi apakah sudah seminar KP
        $seminarDisetujui = auth()->user()->seminarKP()->where('status', 'disetujui')->exists();
        if (!$seminarDisetujui) {
            return back()->with('error', 'Anda harus menyelesaikan seminar KP terlebih dahulu sebelum upload laporan.');
        }
        
        $request->validate([
            'judul_kp_final' => 'required|string|max:255',
            'laporan_akhir' => 'nullable|file|mimes:pdf|max:5120', // Max 5MB
        ]);

        $user = auth()->user();
        $data = [
            'judul_kp_final' => $request->judul_kp_final,
        ];

        // Jika ada file baru, update file
        if ($request->hasFile('laporan_akhir')) {
            $laporan = LaporanKP::where('user_id', $user->id)->first();
            
            // Hapus file lama jika ada
            if ($laporan && $laporan->file_laporan && \Storage::exists($laporan->file_laporan)) {
                \Storage::delete($laporan->file_laporan);
            }
            
            $filePath = $request->file('laporan_akhir')->store('laporan_kp', 'public');
            $data['file_laporan'] = $filePath;
            $data['tanggal_upload'] = now();
            
            // Set status ke menunggu untuk file baru (upload pertama atau setelah ditolak)
            if (!$laporan || $laporan->status_approve === 'ditolak' || $laporan->status_approve !== 'disetujui') {
                $data['status_approve'] = 'menunggu';
                $data['catatan_dosen'] = null;
            }
        }

        LaporanKP::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return back()->with('success', 'Laporan KP berhasil diunggah.');
    }

    // Method lain untuk nilai, dll. bisa ditambah nanti
}
