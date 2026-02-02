<?php

namespace App\Http\Controllers\Koor;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKP;
use App\Models\PenerimaanKP;
use App\Models\SeminarKP;
use App\Models\PembimbinganKP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KoordinatorController extends Controller
{
    public function dashboard()
    {
        $koordinatorId = auth()->id();

        $totalPengajuan = PengajuanKP::count();
        $pengajuanMenunggu = PengajuanKP::where('status', 'menunggu')->count();
        $penerimaanMenunggu = PenerimaanKP::where('status', 'menunggu')->count();
        $seminarMenunggu = SeminarKP::where('status', 'menunggu')->count();
        $totalMahasiswa = PembimbinganKP::where('koordinator_id', $koordinatorId)->count();
        $mahasiswaBelumDosen = PembimbinganKP::where('koordinator_id', $koordinatorId)
            ->whereNull('dosen_pembimbing_id')
            ->count();

        return view('pages.koor.dashboard', compact(
            'totalPengajuan',
            'pengajuanMenunggu',
            'penerimaanMenunggu',
            'seminarMenunggu',
            'totalMahasiswa',
            'mahasiswaBelumDosen'
        ));
    }

    public function pengajuan()
    {
        // Ambil semua pengajuan untuk search client-side
        $allPengajuan = PengajuanKP::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil pengajuan dengan pagination (limit 15 per halaman)
        $pengajuanList = PengajuanKP::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.koor.pengajuanKoor', compact('pengajuanList', 'allPengajuan'));
    }

    public function showPengajuan($id)
    {
        $pengajuan = PengajuanKP::with('user')->findOrFail($id);

        return view('pages.koor.pengajuan_detail', compact('pengajuan'));
    }

    // Daftar penerimaan KP untuk koordinator (pagination 15)
    public function penerimaan()
    {
        $penerimaanList = PenerimaanKP::with(['user', 'pengajuanKP'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.koor.penerimaanKoor', compact('penerimaanList'));
    }

    // Ubah status penerimaan KP (setujui / tolak)
    public function updateStatusPenerimaan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $penerimaan = PenerimaanKP::findOrFail($id);
        $penerimaan->status = $request->status;
        if ($request->filled('catatan')) {
            $penerimaan->catatan = $request->catatan;
        }
        $penerimaan->save();

        $msg = $request->status === 'diterima' ? 'Penerimaan KP disetujui.' : 'Penerimaan KP ditolak.';
        return back()->with('success', $msg);
    }

    // Hapus data penerimaan KP
    public function destroyPenerimaan($id)
    {
        $penerimaan = PenerimaanKP::findOrFail($id);

        // Hapus file surat penerimaan jika ada
        if ($penerimaan->file_surat_penerimaan) {
            Storage::disk('public')->delete($penerimaan->file_surat_penerimaan);
        }

        $penerimaan->delete();

        return back()->with('success', 'Data penerimaan KP berhasil dihapus.');
    }

    public function destroyPengajuan($id)
    {
        $pengajuan = PengajuanKP::findOrFail($id);

        // Hapus file jika ada
        if ($pengajuan->file_surat_pengajuan) {
            Storage::disk('public')->delete($pengajuan->file_surat_pengajuan);
        }
        if ($pengajuan->file_surat_penerimaan) {
            Storage::disk('public')->delete($pengajuan->file_surat_penerimaan);
        }

        $pengajuan->delete();

        return back()->with('success', 'Pengajuan KP berhasil dihapus oleh koordinator.');
    }

    // Seminar KP (Koordinator)
    public function seminar()
    {
        $seminarList = SeminarKP::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.koor.seminarKoor', compact('seminarList'));
    }

    public function updateStatusSeminar(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $seminar = SeminarKP::findOrFail($id);
        $seminar->status = $request->status;
        
        if ($request->filled('catatan')) {
            $seminar->catatan = $request->catatan;
        }
        
        $seminar->save();

        $msg = $request->status === 'disetujui' ? 'Seminar KP disetujui.' : 'Seminar KP ditolak.';
        return back()->with('success', $msg);
    }

    public function destroySeminar($id)
    {
        $seminar = SeminarKP::findOrFail($id);
        $seminar->delete();

        return back()->with('success', 'Pendaftaran seminar berhasil dihapus.');
    }
    public function dataMahasiswa()
    {
        $koordinatorId = auth()->id();
        
        // Ambil mahasiswa yang di bawah koordinasi ini dengan eager load penerimaan KP yang diterima
        $mahasiswaList = PembimbinganKP::where('koordinator_id', $koordinatorId)
            ->with(['mahasiswa.penerimaanKP' => function($query) {
                $query->where('status', 'diterima');
            }, 'dosenPembimbing'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Daftar dosen untuk dropdown assign
        $dosenList = User::whereJsonContains('roles', 'dosen')->get();
        
        return view('pages.koor.data_mahasiswa', compact('mahasiswaList', 'dosenList'));
    }

    public function assignPembimbing(Request $request, $id)
    {
        $request->validate([
            'dosen_pembimbing_id' => 'required|exists:users,id',
            'catatan' => 'nullable|string|max:500',
        ]);

        $pembimbingan = PembimbinganKP::findOrFail($id);
        
        // Validasi bahwa ini mahasiswa di bawah koordinator ini
        if ($pembimbingan->koordinator_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $pembimbingan->dosen_pembimbing_id = $request->dosen_pembimbing_id;
        if ($request->filled('catatan')) {
            $pembimbingan->catatan = $request->catatan;
        }
        $pembimbingan->save();

        return back()->with('success', 'Dosen pembimbing berhasil di-assign.');
    }

    public function rekap()
    {
        $koordinatorId = auth()->id();
        
        // Ambil semua mahasiswa yang di bawah koordinasi ini
        $rekapList = PembimbinganKP::where('koordinator_id', $koordinatorId)
            ->with([
                'mahasiswa',
                'mahasiswa.laporanKP' => function($query) {
                    $query->latest();
                },
                'mahasiswa.penerimaanKP' => function($query) {
                    $query->where('status', 'diterima')->latest();
                },
                'dosenPembimbing'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('pages.koor.rekapKoor', compact('rekapList'));
    }
}
