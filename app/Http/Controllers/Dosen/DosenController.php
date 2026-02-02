<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PembimbinganKP;
use App\Models\LaporanKP;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DosenController extends Controller
{
    // Dashboard Dosen
    public function dashboard()
    {
        $dosenId = auth()->id();
        
        // Total mahasiswa bimbingan
        $totalMahasiswaBimbingan = PembimbinganKP::where('dosen_pembimbing_id', $dosenId)->count();
        
        // Laporan KP menunggu persetujuan
        $laporanMenungguPersetujuan = LaporanKP::whereHas('mahasiswa.pembimbingan', function($query) use ($dosenId) {
            $query->where('dosen_pembimbing_id', $dosenId);
        })->where('status_approve', 'menunggu')->count();
        
        // Laporan KP sudah disetujui
        $laporanDisetujui = LaporanKP::whereHas('mahasiswa.pembimbingan', function($query) use ($dosenId) {
            $query->where('dosen_pembimbing_id', $dosenId);
        })->where('status_approve', 'disetujui')->count();
        
        // Laporan KP belum ada nilai
        $laporanBelumBernilai = LaporanKP::whereHas('mahasiswa.pembimbingan', function($query) use ($dosenId) {
            $query->where('dosen_pembimbing_id', $dosenId);
        })->where('status_approve', 'disetujui')->whereNull('nilai')->count();
        
        return view('pages.dosen.dashboard', compact(
            'totalMahasiswaBimbingan',
            'laporanMenungguPersetujuan',
            'laporanDisetujui',
            'laporanBelumBernilai'
        ));
    }

    // Daftar Mahasiswa Bimbingan (Dosen)
    public function bimbingan()
    {
        $dosenId = auth()->id();
        
        // Ambil mahasiswa yang dibimbing oleh dosen ini dengan penerimaan KP yang diterima
        $mahasiswaList = PembimbinganKP::where('dosen_pembimbing_id', $dosenId)
            ->with(['mahasiswa.penerimaanKP' => function($query) {
                $query->where('status', 'diterima');
            }, 'koordinator'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('pages.dosen.bimbingan', compact('mahasiswaList'));
    }

    // Detail Mahasiswa Bimbingan
    public function show($id)
    {
        $pembimbingan = PembimbinganKP::with(['mahasiswa', 'koordinator', 'dosenPembimbing'])
            ->findOrFail($id);
        
        // Validasi bahwa ini mahasiswa bimbingan dosen ini
        if ($pembimbingan->dosen_pembimbing_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        return view('pages.dosen.mahasiswa_detail', compact('pembimbingan'));
    }

    // Daftar Laporan KP Mahasiswa Bimbingan
    public function laporan()
    {
        $dosenId = auth()->id();
        
        // Ambil laporan KP terbaru dari mahasiswa yang dibimbing oleh dosen ini
        $laporanList = LaporanKP::whereHas('mahasiswa.pembimbingan', function($query) use ($dosenId) {
            $query->where('dosen_pembimbing_id', $dosenId);
        })
        ->with(['mahasiswa'])
        ->orderBy('tanggal_upload', 'desc')
        ->paginate(15);
        
        return view('pages.dosen.laporanDosen', compact('laporanList'));
    }

    // Download Laporan KP
    public function downloadLaporan($id)
    {
        $laporan = LaporanKP::findOrFail($id);
        
        // Validasi bahwa laporan ini milik mahasiswa bimbingan dosen ini
        if (!$laporan->mahasiswa->pembimbingan || $laporan->mahasiswa->pembimbingan->dosen_pembimbing_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        
        $path = storage_path('app/public/' . $laporan->file_laporan);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'pdf';
        $nimOrName = $laporan->mahasiswa->nim ?: Str::slug($laporan->mahasiswa->name);
        $safeTitle = Str::slug($laporan->judul_kp_final);
        $filename = $nimOrName . '-' . $safeTitle . '.' . $ext;

        return response()->download($path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // Preview/Lihat Laporan KP (inline)
    public function viewLaporan($id)
    {
        $laporan = LaporanKP::findOrFail($id);

        // Validasi bahwa laporan ini milik mahasiswa bimbingan dosen ini
        if (!$laporan->mahasiswa->pembimbingan || $laporan->mahasiswa->pembimbingan->dosen_pembimbing_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $path = storage_path('app/public/' . $laporan->file_laporan);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // Update status persetujuan laporan
    public function updateStatusLaporan(Request $request, $id)
    {
        $request->validate([
            'status_approve' => 'required|in:disetujui,ditolak',
            'catatan_dosen' => 'nullable|string|max:500',
        ]);

        $laporan = LaporanKP::findOrFail($id);

        // Validasi otorisasi
        if (!$laporan->mahasiswa->pembimbingan || $laporan->mahasiswa->pembimbingan->dosen_pembimbing_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $laporan->update([
            'status_approve' => $request->status_approve,
            'catatan_dosen' => $request->catatan_dosen,
        ]);

        // Create log entry
        \App\Models\LaporanKPLog::create([
            'laporan_kp_id' => $laporan->id,
            'dosen_id' => auth()->id(),
            'aksi' => $request->status_approve === 'disetujui' ? 'approve' : 'reject',
            'catatan' => $request->catatan_dosen,
        ]);

        $msg = $request->status_approve === 'disetujui' 
            ? 'Laporan KP disetujui.' 
            : 'Laporan KP ditolak.';

        return back()->with('success', $msg);
    }

    // Update nilai laporan
    public function updateNilaiLaporan(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'catatan_dosen' => 'nullable|string|max:500',
        ]);

        $laporan = LaporanKP::findOrFail($id);

        // Validasi otorisasi
        if (!$laporan->mahasiswa->pembimbingan || $laporan->mahasiswa->pembimbingan->dosen_pembimbing_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Nilai hanya bisa diberikan jika laporan sudah disetujui
        if ($laporan->status_approve !== 'disetujui') {
            return back()->withErrors(['error' => 'Laporan harus disetujui terlebih dahulu sebelum memberi nilai.']);
        }

        $laporan->update([
            'nilai' => $request->nilai,
            'catatan_dosen' => $request->catatan_dosen,
        ]);

        return back()->with('success', 'Nilai laporan KP berhasil disimpan. Nilai: ' . $request->nilai);
    }

    // Hapus laporan KP secara permanen
    public function destroyLaporan($id)
    {
        $laporan = LaporanKP::findOrFail($id);

        // Validasi otorisasi
        if (!$laporan->mahasiswa->pembimbingan || $laporan->mahasiswa->pembimbingan->dosen_pembimbing_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Simpan nama mahasiswa untuk pesan
        $mahasiswaName = $laporan->mahasiswa->name;

        // Hapus file laporan jika ada
        if ($laporan->file_laporan && \Storage::exists($laporan->file_laporan)) {
            \Storage::delete($laporan->file_laporan);
        }

        // Hapus data laporan
        $laporan->delete();

        return back()->with('success', 'Laporan KP dari ' . $mahasiswaName . ' berhasil dihapus.');
    }
}
