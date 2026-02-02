<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    public function index()
    {
        $perusahaanList = Perusahaan::orderBy('nama_perusahaan')->paginate(15);
        return view('pages.admin.data-perusahaan', compact('perusahaanList'));
    }

    public function create()
    {
        return view('pages.admin.perusahaan-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255|unique:perusahaans,nama_perusahaan',
            'is_mitra' => 'required|boolean',
        ]);

        Perusahaan::create($validated);
        return redirect()->route('admin.perusahaan.index')->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function edit(Perusahaan $perusahaan)
    {
        return view('pages.admin.perusahaan-edit', compact('perusahaan'));
    }

    public function update(Request $request, Perusahaan $perusahaan)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255|unique:perusahaans,nama_perusahaan,' . $perusahaan->id,
            'is_mitra' => 'required|boolean',
        ]);

        $perusahaan->update($validated);
        return redirect()->route('admin.perusahaan.index')->with('success', 'Perusahaan berhasil diperbarui.');
    }

    public function destroy(Perusahaan $perusahaan)
    {
        $perusahaan->delete();
        return redirect()->route('admin.perusahaan.index')->with('success', 'Perusahaan berhasil dihapus.');
    }
}
