<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * OTENTIKASI: register, login (Auth::attempt + hash bcrypt),
 * logout dengan invalidasi session & regenerasi token CSRF.
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Cegah session fixation
            $request->session()->regenerate();

            $user = Auth::user();

            return redirect()
                ->intended($user->isAdmin() ? route('admin.dashboard') : route('home'))
                ->with('success', 'Selamat datang kembali, ' . $user->name . '! 🥬');
        }

        return back()
            ->withErrors(['email' => 'Email atau kata sandi salah.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:25'],
            'gender'   => ['required', 'in:L,P'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'terms'    => ['accepted'],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan login.',
            'phone.required'     => 'Nomor HP wajib diisi.',
            'gender.required'    => 'Pilih jenis kelamin.',
            'gender.in'          => 'Jenis kelamin tidak valid.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.min'       => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'terms.accepted'     => 'Anda harus menyetujui syarat & ketentuan.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'gender'   => $validated['gender'],
            'password' => Hash::make($validated['password']),
            'role'     => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'Akun berhasil dibuat. Selamat berbelanja, ' . $user->name . '! 🎉');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah keluar. Sampai jumpa lagi!');
    }
}
