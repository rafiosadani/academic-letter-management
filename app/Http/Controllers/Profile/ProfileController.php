<?php

namespace App\Http\Controllers\Profile;

use App\Enums\NotificationCategory;
use App\Enums\PermissionName;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\NotificationSetting;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(Request $request): View
    {
        $this->authorizePermission(PermissionName::PROFILE_VIEW->value);

        $user = Auth::user()->load('profile.studyProgram');
        $studyPrograms = $this->getStudyPrograms();

        $notificationCategories = NotificationCategory::cases();
        $notificationSettings = [];
        foreach ($notificationCategories as $category) {
            $notificationSettings[$category->value] = NotificationSetting::getOrCreate($user->id, $category->value);
        }

        $activeTab = $request->query('tab', 'tab-account');

        return view('profile.edit', compact('activeTab', 'user', 'studyPrograms', 'notificationCategories', 'notificationSettings'));
    }

    /**
     * Update user profile (Tab 1: Account).
     */
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $isMahasiswa = $user->hasRole('Mahasiswa');

        try {
            DB::transaction(function () use ($request, $user, $isMahasiswa) {
                // 1. Update User Table (Email)
                $user->update([
                    'email' => $request->email,
                ]);

                // 2. Handle Photo Upload
                $photoPath = $user->profile->photo;

                if ($request->hasFile('photo')) {
                    if ($user->profile->photo && $user->profile->photo !== 'default.png') {
                        Storage::disk('public')->delete($user->profile->photo);
                    }

                    // Store new photo
                    $photoPath = $request->file('photo')->store('users/photos', 'public');
                }

                $profileData = [
                    'full_name' => $request->full_name,
                    'place_of_birth' => $request->place_of_birth,
                    'date_of_birth' => $request->date_of_birth,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'photo' => $photoPath,
                ];

                // 3. Logic Kondisional Role
                if ($isMahasiswa) {
                    $profileData['student_or_employee_id'] = $user->profile->student_or_employee_id;
                    $profileData['study_program_id'] = $user->profile->study_program_id;

                    $profileData['parent_name'] = $request->parent_name;
                    $profileData['parent_nip'] = $request->parent_nip;
                    $profileData['parent_rank'] = $request->parent_rank;
                    $profileData['parent_institution'] = $request->parent_institution;
                    $profileData['parent_institution_address'] = $request->parent_institution_address;
                } else {
                    $profileData['student_or_employee_id'] = $request->student_or_employee_id;
                    $profileData['study_program_id'] = $request->study_program_id;
                }

                $user->profile->update($profileData);
            });

            // LOG SUCCESS
            LogHelper::logSuccess('updated', 'profile', [
                'user_id' => $user->id,
                'user_name' => $user->profile->full_name,
            ], $request);

            return redirect()->back()->with([
                'active_tab' => 'tab-account',
                'notification_data' => [
                    'type' => 'success',
                    'text' => 'Profil berhasil diperbarui!',
                    'position' => 'center-top',
                    'duration' => 4000
                ]
            ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update', 'profile', $e, [
                'request_data' => $request->except(['_token', 'photo'])
            ], $request);

            return redirect()->back()->withInput()->with([
                'active_tab' => 'tab-account',
                'notification_data' => [
                    'type' => 'error',
                    'text' => 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage(),
                    'position' => 'center-top',
                    'duration' => 6000,
                ]
            ]);
        }
    }

    /**
     * Update user password (Tab 3: Security).
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();

        try {
            // Update password
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            // LOG SUCCESS
            LogHelper::logSuccess('updated', 'password', [
                'user_id' => $user->id,
                'user_name' => $user->profile->full_name,
            ], $request);

            return redirect()->back()->with([
                'active_tab' => 'tab-security',
                'notification_data' => [
                    'type' => 'success',
                    'text' => 'Password berhasil diubah!',
                    'position' => 'center-top',
                    'duration' => 4000
                ]
            ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update', 'password', $e, [], $request);

            return redirect()->back()->with([
                'active_tab' => 'tab-security',
                'notification_data' => [
                    'type' => 'error',
                    'text' => 'Terjadi kesalahan saat mengubah password.',
                    'position' => 'center-top',
                    'duration' => 6000,
                ]
            ]);
        }
    }

    /**
     * Get study programs for dropdown.
     */
    protected function getStudyPrograms()
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

    protected function authorizePermission(string $permission)
    {
        if (!Auth::user()->hasPermissionTo($permission)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
    }
}
