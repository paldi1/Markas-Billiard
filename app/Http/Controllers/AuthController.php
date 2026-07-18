<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        if (session()->has('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin_login');
    }

    // Memproses data login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek password dari session, jika belum pernah diganti pakai default 'admin123'
        $passwordTersimpan = session('admin_password', 'admin123');

        if ($request->username === 'admin' && $request->password === $passwordTersimpan) {
            session(['admin_logged_in' => true]);
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, Admin!');
        }

        return redirect()->back()->withErrors(['login_error' => 'Username atau Password salah!'])->withInput();
    }

    // Memproses logout
    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil logout.');
    }

    // --- TAMBAHAN FITUR GANTI SANDI ---

    public function showChangePassword()
    {
        // Bisa mengarahkan ke view baru, atau gunakan modal di dashboard
        return view('admin_ganti_sandi'); 
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:3', // minimal 3 karakter
            'confirm_password' => 'required|same:new_password',
        ]);

        $passwordTersimpan = session('admin_password', 'admin123');

        // Cek apakah password lama sesuai
        if ($request->old_password !== $passwordTersimpan) {
            return back()->withErrors(['old_password' => 'Password lama salah!']);
        }

        // Simpan password baru ke session
        session(['admin_password' => $request->new_password]);

        return redirect()->route('admin.dashboard')->with('success', 'Password berhasil diubah!');
    }
}