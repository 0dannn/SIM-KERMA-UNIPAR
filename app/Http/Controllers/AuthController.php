<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan Halaman Login SIMKERMA
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('simkerma.index');
        }

        return view('auth.login');
    }

    /**
     * Proses Autentikasi Pengguna
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Set role aktif sesuai peran akun yang login
            session(['active_role' => $user->role]);

            return redirect()->intended(route('simkerma.index'))
                ->with('flash', [
                    'type' => 'success',
                    'msg' => "Autentikasi Berhasil! Selamat datang, {$user->name} (Role: {$user->role})."
                ]);
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Logout Pengguna
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('flash', [
                'type' => 'info',
                'msg' => 'Anda telah berhasil keluar dari sistem SIMKERMA.'
            ]);
    }
}
