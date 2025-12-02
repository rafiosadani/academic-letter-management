<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LogHelper;
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
            $user = Auth::user();

            $userName = $user->profile?->full_name ?? $user->email;

            // LOG INFO
            LogHelper::logInfo('User logged in successfully', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->roles->first()?->name ?? 'N/A',
            ], $request);

            $request->session()->regenerate();

            $request->session()->flash('alert_show_id', 'alert-login-success');
            $request->session()->flash('notification_data', [
                'type' => 'success',
                'text' => 'Selamat Datang Kembali, ' . $userName . '!. Sistem siap digunakan.',
                'position' => 'center-top',
                'duration' => 4000
            ]);
            return redirect()->intended(route('dashboard'));
        }

        // LOG WARNING
        LogHelper::logWarning('Failed login attempt with invalid credentials', [
            'email_attempt' => $request->email,
        ], $request);

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
