<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = Auth::id();
        $userName = Auth::user()->name ?? 'Unknown';

        Auth::logout();

        // LOG INFO
        LogHelper::logInfo('User logged out successfully', [
            'user_id_logged_out' => $userId,
            'user_name_logged_out' => $userName,
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->flash('alert_show_id', 'alert-logout-success');

        return redirect(route('login'));
    }
}
