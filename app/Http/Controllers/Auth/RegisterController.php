<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:20|unique:users',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/@mahasiswa\.pcr\.ac\.id$/',
            ],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.regex' => 'Domain email salah. Gunakan domain @mahasiswa.pcr.ac.id',
            'email.unique' => 'Email sudah terdaftar.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roles' => ['mahasiswa'],
        ]);

        // Auto login setelah register
        auth()->login($user);

        return redirect('/');
    }
}
