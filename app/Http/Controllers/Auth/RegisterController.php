<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\CodeGeneration;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        $studyPrograms = $this->getStudyPrograms();

        return view('auth.register', compact('studyPrograms'));
    }

    /**
     * Handle registration request.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = null;

        try {
            DB::transaction(function () use ($request, &$user) {
                // 1. Generate User Code
                $code = (new CodeGeneration(User::class, 'code', 'USR'))->getGeneratedCode();

                // 2. Create User
                $user = User::create([
                    'code' => $code,
                    'email' => $request->email,
                    'password' => $request->password,
                    'status' => 1,
                ]);

                // 3. Create User Profile
                $user->profile()->create([
                    'full_name' => $request->full_name,
                    'student_or_employee_id' => $request->student_or_employee_id,
                    'study_program_id' => $request->study_program_id,
                    'photo' => 'default.png', // Default photo
                ]);

                // 4. Assign Role "Mahasiswa"
                $user->assignRole('mahasiswa');
            });

            // LOG SUCCESS
            LogHelper::logSuccess('registered', 'user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => 'Mahasiswa',
                'user_full_name' => $user->profile->full_name,
            ], $request);

            $request->session()->flash('notification_data', [
                'type'     => 'success',
                'text'     => 'Registrasi berhasil. Silakan login menggunakan akun Anda.',
                'position' => 'center-top',
                'duration' => 4000,
            ]);

            return redirect()->route('login');
        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('register', 'user', $e, [
                'request_data' => $request->except(['_token', 'password', 'password_confirmation'])
            ], $request);

            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.',
                    'position' => 'center-top',
                    'duration' => 6000,
                ]);
        }
    }

    public function getStudyPrograms()
    {
        return StudyProgram::withoutTrashed()
            ->select('id', 'degree', 'name')
            ->orderBy('degree')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($program) {
                return [$program->id => $program->degree_name];
            });
    }
}
