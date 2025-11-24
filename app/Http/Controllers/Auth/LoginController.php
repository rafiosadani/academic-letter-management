<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index() {
        return view('auth.login');
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if(Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $request->session()->flash('alert_show_id', 'alert-login-success');
            $request->session()->flash('notification_data', [
                'type' => 'success',
                'text' => 'Selamat Datang Kembali, ' . Auth::user()->name . '! Login berhasil. Sistem siap digunakan.',
                'position' => 'center-top',
                'duration' => 4000
            ]);
            return redirect()->intended(route('dashboard'));
        }

        $request->session()->flash('notification_data', [
            'type' => 'error',
            'text' => 'Otentikasi gagal. Silakan periksa kredensial Anda.',
            'position' => 'center-top',
            'duration' => 4000
        ]);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }
}
