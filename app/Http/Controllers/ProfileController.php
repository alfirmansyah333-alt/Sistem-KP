<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // Tampilkan form edit profil
    public function edit()
    {
        $user = auth()->user();
        return view('pages.profile.edit', compact('user'));
    }

    // Simpan perubahan profil
    public function update(Request $request)
    {
        $user = auth()->user();
        $emailDomain = explode('@', $user->email)[1];
        $newEmail = $request->email_username . '@' . $emailDomain;

        $request->validate([
            'name' => 'required|string|max:255',
            'email_username' => 'required|string|max:255',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $newEmail,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    // Upload foto profil
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        // Hapus foto lama jika ada
        if ($user->profile_photo_path && \Storage::exists('public/' . $user->profile_photo_path)) {
            \Storage::delete('public/' . $user->profile_photo_path);
        }

        // Resize dan simpan foto
        $file = $request->file('profile_photo');
        $filename = 'profile_photos/' . uniqid() . '.' . $file->getClientOriginalExtension();
        \Storage::disk('public')->put($filename, file_get_contents($file->getRealPath()));

        $user->update(['profile_photo_path' => $filename]);

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil diubah.');
    }

    // Hapus foto profil
    public function deletePhoto()
    {
        $user = auth()->user();

        if ($user->profile_photo_path && \Storage::exists('public/' . $user->profile_photo_path)) {
            \Storage::delete('public/' . $user->profile_photo_path);
        }

        $user->update(['profile_photo_path' => null]);

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil dihapus.');
    }

    // Tampilkan form ubah password
    public function changePassword()
    {
        return view('pages.profile.change-password');
    }

    // Simpan perubahan password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diubah.');
    }
}
