<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanKP;
use App\Models\PenerimaanKP;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalMahasiswa = User::whereJsonContains('roles', 'mahasiswa')->count();
        $totalDosen = User::whereJsonContains('roles', 'dosen')->count();
        $totalPerusahaan = PenerimaanKP::whereNotNull('nama_perusahaan')
            ->distinct('nama_perusahaan')
            ->count('nama_perusahaan');

        return view('pages.admin.dashboard', compact(
            'totalMahasiswa',
            'totalDosen',
            'totalPerusahaan'
        ));
    }

    public function dataMahasiswa()
    {
        $mahasiswaList = User::whereJsonContains('roles', 'mahasiswa')
            ->with([
                'penerimaanKP' => function ($query) {
                    $query->where('status', 'diterima')->latest();
                },
                'laporanKP' => function ($query) {
                    $query->latest();
                },
                'seminarKP' => function ($query) {
                    $query->latest();
                },
                'pembimbingan.dosenPembimbing',
            ])
            ->orderBy('name')
            ->paginate(15);

        return view('pages.admin.data-mahasiswa', compact('mahasiswaList'));
    }

    public function exportDataMahasiswa(): StreamedResponse
    {
        $mahasiswaList = User::whereJsonContains('roles', 'mahasiswa')
            ->with([
                'penerimaanKP' => function ($query) {
                    $query->where('status', 'diterima')->latest();
                },
                'laporanKP' => function ($query) {
                    $query->latest();
                },
                'seminarKP' => function ($query) {
                    $query->latest();
                },
                'pembimbingan.dosenPembimbing',
            ])
            ->orderBy('name')
            ->get();

        $filename = 'data_mahasiswa_kp_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use ($mahasiswaList) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'No',
                'NIM',
                'Nama',
                'Nama Perusahaan',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Pembimbing',
                'Judul KP',
                'Tanggal Seminar',
                'Nilai KP',
            ]);

            $no = 1;
            foreach ($mahasiswaList as $mhs) {
                $penerimaan = $mhs->penerimaanKP->first();
                $laporan = $mhs->laporanKP->first();
                $seminar = $mhs->seminarKP->first();
                $pembimbing = $mhs->pembimbingan?->dosenPembimbing?->name;

                $tanggalMulai = $penerimaan?->tanggal_mulai ? $penerimaan->tanggal_mulai->format('d M Y') : '-';
                $tanggalSelesai = $penerimaan?->tanggal_selesai ? $penerimaan->tanggal_selesai->format('d M Y') : '-';
                $tanggalSeminar = $seminar?->tanggal_seminar ? $seminar->tanggal_seminar->format('d M Y') : '-';
                $nilai = $laporan?->nilai ? number_format($laporan->nilai, 2) : '-';

                fputcsv($handle, [
                    $no++,
                    $mhs->nim ?? '-',
                    $mhs->name ?? '-',
                    $penerimaan->nama_perusahaan ?? '-',
                    $tanggalMulai,
                    $tanggalSelesai,
                    $pembimbing ?? '-',
                    $laporan->judul_kp_final ?? '-',
                    $tanggalSeminar,
                    $nilai,
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }

    public function dataPerusahaan()
    {
        $perusahaanList = PenerimaanKP::select('nama_perusahaan')
              ->selectRaw('COUNT(DISTINCT penerimaan_kps.user_id) as jumlah_mahasiswa')
              ->selectRaw("MAX(CASE WHEN pengajuan_kp.mitra_dengan_perusahaan = 'iya' THEN 1 ELSE 0 END) as is_mitra")
              ->leftJoin('pengajuan_kp', 'pengajuan_kp.id', '=', 'penerimaan_kps.pengajuan_kp_id')
              ->whereNotNull('penerimaan_kps.nama_perusahaan')
              ->groupBy('penerimaan_kps.nama_perusahaan')
              ->orderBy('penerimaan_kps.nama_perusahaan')
              ->get();

        return view('pages.admin.data-perusahaan', compact('perusahaanList'));
    }

    public function dataStaff()
    {
        $staffList = User::whereJsonContains('roles', 'dosen')
            ->orWhereJsonContains('roles', 'koor')
            ->orWhereJsonContains('roles', 'koordinator')
            ->orderBy('name')
            ->paginate(15);

        return view('pages.admin.data-staff', compact('staffList'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'nidn' => 'required|string|max:50',
            'password' => 'required|string|min:6',
            'roles' => 'required|array|min:1',
            'roles.*' => 'in:dosen,koor',
        ]);

        $roles = array_unique($validated['roles']);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nidn' => $validated['nidn'] ?? null,
            'password' => $validated['password'],
            'roles' => $roles,
        ]);

        return back()->with('success', 'Akun berhasil ditambahkan.');
    }

    public function updateRole(Request $request, $id)
    {
        $validated = $request->validate([
            'roles' => 'required|array|min:1',
            'roles.*' => 'in:dosen,koor',
        ]);

        $user = User::findOrFail($id);

        if (in_array('admin', $user->roles ?? [])) {
            return back()->with('error', 'Tidak bisa mengubah role admin.');
        }

        $existingRoles = $user->roles ?? [];
        $baseRoles = array_values(array_diff($existingRoles, ['dosen', 'koor', 'koordinator']));
        $newRoles = array_values(array_unique(array_merge($baseRoles, $validated['roles'])));

        $user->roles = $newRoles;
        $user->save();

        return back()->with('success', 'Role berhasil diperbarui.');
    }

    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'nidn' => 'required|string|max:50',
        ]);

        $user = User::findOrFail($id);

        if (in_array('admin', $user->roles ?? [])) {
            return back()->with('error', 'Tidak bisa mengubah data admin.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->nidn = $validated['nidn'];
        $user->save();

        return back()->with('success', 'Data akun berhasil diperbarui.');
    }
    
    public function exportDataPerusahaan(): StreamedResponse
    {
        $perusahaanList = PenerimaanKP::select('penerimaan_kps.nama_perusahaan')
            ->selectRaw('COUNT(DISTINCT penerimaan_kps.user_id) as jumlah_mahasiswa')
            ->selectRaw("MAX(CASE WHEN pengajuan_kp.mitra_dengan_perusahaan = 'iya' THEN 1 ELSE 0 END) as is_mitra")
            ->leftJoin('pengajuan_kp', 'pengajuan_kp.id', '=', 'penerimaan_kps.pengajuan_kp_id')
            ->whereNotNull('penerimaan_kps.nama_perusahaan')
            ->groupBy('penerimaan_kps.nama_perusahaan')
            ->orderBy('penerimaan_kps.nama_perusahaan')
            ->get();

        $filename = 'data_perusahaan_kp_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use ($perusahaanList) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'No',
                'Nama Perusahaan',
                'Mitra',
                'Jumlah Mahasiswa',
            ]);

            $no = 1;
            foreach ($perusahaanList as $perusahaan) {
                $mitraLabel = ((int)($perusahaan->is_mitra ?? 0) === 1) ? 'Ya' : 'Tidak';
                fputcsv($handle, [
                    $no++,
                    $perusahaan->nama_perusahaan ?? '-',
                    $mitraLabel,
                    ($perusahaan->jumlah_mahasiswa ?? 0) . ' Mahasiswa',
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }
}
