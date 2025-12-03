<?php

namespace App\Http\Controllers\Master;

use App\Helpers\CodeGeneration;
use App\Helpers\LogHelper;
use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\User\CreateUserRequest;
use App\Http\Requests\Master\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['createdByUser', 'profile', 'roles'])
            ->orderBy('code', 'desc');

        if ($request->has('view_deleted')) {
            $query->onlyTrashed();
        }

        $users = $query->filter($request->only('search'))
            ->paginate(4)
            ->withQueryString();

        return view('master.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::withoutTrashed()->orderBy('name')->get();
        $studyPrograms = $this->getStudyPrograms();

        return view('master.users.form', compact('roles', 'studyPrograms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        $user = null;
        try {
            DB::transaction(function () use ($request, &$user) {
                $code = (new CodeGeneration(User::class, 'code', 'USR'))->getGeneratedCode();

                $user = User::create([
                    'code' => $code,
                    'email' => $request->email,
                    'password' => $request->password,
                    'status' => $request->status,
                ]);

                $photoPath = null;
                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')->store('users/photos', 'public');
                }

                if ($photoPath === null) {
                    $photoPath = 'default.png';
                }

                $user->profile()->create([
                    'full_name' => $request->full_name,
                    'student_or_employee_id' => $request->student_or_employee_id,
                    'phone' => $request->phone,
                    'photo' => $photoPath,
                    'study_program_id' => $request->study_program_id,
                    'address' => $request->address,
                ]);

                $role = Role::findOrFail($request->role_id);
                $user->assignRole($role);
            });

            // LOG SUCCESS
            LogHelper::logSuccess('created', 'user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->roles->first()?->name,
                'user_full_name' => $user->profile->full_name,
            ], $request);

            return redirect()->route('master.users.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => "User {$user->profile->full_name} berhasil ditambahkan!",
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('create', 'user', $e, [
                'request_data' => $request->except(['_token', 'password', 'photo'])
            ], $request);

            return redirect()->back()->withInput()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'position' => 'center-top',
                    'duration' => 6000,
                ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $groupedPermissions = PermissionHelper::getGroupedPermissions();
        $roles = Role::withoutTrashed()->orderBy('name')->get();
        $studyPrograms = $this->getStudyPrograms();

        $user->load('profile', 'roles');

        return view('master.users.show', compact('groupedPermissions', 'roles', 'studyPrograms', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::withoutTrashed()->orderBy('name')->get();
        $studyPrograms = $this->getStudyPrograms();

        $user->load('profile', 'roles');

        return view('master.users.form', compact('roles', 'studyPrograms', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $oldRoleName = $user->roles->first()?->name;
        $oldStatus = $user->status;

        try {
            DB::transaction(function () use ($request, $user) {
                $userData = [
                    'email' => $request->email,
                    'status' => $request->status,
                ];

                if ($request->filled('password')) {
                    $userData['password'] = $request->password;
                }

                $user->update($userData);

                $photoPath = $user->profile->photo;

                if ($request->hasFile('photo')) {
                    if ($photoPath && $photoPath !== 'default.png' && Storage::disk('public')->exists($photoPath)) {
                        Storage::disk('public')->delete($photoPath);
                    }

                    $photoPath = $request->file('photo')->store('users/photos', 'public');
                }

                $user->profile()->update([
                    'full_name' => $request->full_name,
                    'student_or_employee_id' => $request->student_or_employee_id,
                    'phone' => $request->phone,
                    'photo' => $photoPath,
                    'study_program_id' => $request->study_program_id,
                    'address' => $request->address,
                ]);

                $role = Role::findOrFail($request->role_id);
                $user->syncRoles([$role]);
            });

            $newRoleName = $user->roles->first()?->name;
            $newStatus = $user->status;

            // LOG SUCCESS
            LogHelper::logSuccess('updated', 'user', [
                'user_id' => $user->id,
                'old_email' => $oldStatus,
                'new_email' => $newStatus,
                'old_role' => $oldRoleName,
                'new_role' => $newRoleName,
                'user_full_name' => $user->profile->full_name,
            ], $request);

            return redirect()->route('master.users.index')
                ->with('notification_data', [
                    'type' => 'success',
                    'text' => "Data User {$user->profile->full_name} berhasil diperbarui!",
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update', 'user', $e, [
                'user_id' => $user->id,
                'request_data' => $request->except(['_token', 'password', 'photo'])
            ], $request);

            return redirect()->back()->withInput()
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'position' => 'center-top',
                    'duration' => 6000,
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $fullName = $user->profile ? $user->profile->full_name : 'ID ' . $user->id;

        try {
            DB::transaction(function () use ($user) {
                $user->delete();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('deleted', 'user', [
                'user_id' => $user->id,
                'user_full_name' => $fullName,
            ]);

            // Opsional: Kirim data notifikasi toast
            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Data User {$fullName} berhasil dihapus.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.users.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('delete', 'user', $e, [
                'user_id' => $user->id,
            ]);

            return back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Gagal menghapus User. Mungkin User ini sedang digunakan oleh data lain.',
                'position' => 'center-top',
                'duration' => 6000
            ]);
        }
    }

    /**
     * Restore a soft deleted role.
     */
    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->with('profile')->findOrFail($id);
            $fullName = $user->profile ? $user->profile->full_name : 'ID ' . $user->id;

            DB::transaction(function () use ($user) {
                $user->restore();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('restored', 'user', [
                'user_id' => $user->id,
                'user_full_name' => $fullName,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "User {$fullName} berhasil direstore.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('restore', 'user', $e, [
                'user_id' => $id,
            ]);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat restore user.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    /**
     * Restore all soft deleted roles.
     */
    public function restoreAll()
    {
        try {
            $count = User::onlyTrashed()->count();

            if ($count === 0) {
                session()->flash('notification_data', [
                    'type' => 'info',
                    'text' => 'Tidak ada user yang perlu direstore.',
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

                return redirect()->back();
            }

            DB::transaction(function () {
                User::onlyTrashed()->restore();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('restored all', 'user', [
                'restored_count' => $count,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Berhasil restore {$count} user.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.users.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('restore all', 'user', $e);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat restore semua user.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    /**
     * Permanently delete a soft deleted role.
     */
    public function forceDelete($id)
    {
        try {
            $user = User::onlyTrashed()->with('profile')->findOrFail($id);

            $fullName = $user->profile ? $user->profile->full_name : 'ID ' . $user->id;
            $userId = $user->id;

            $photoPath = $user->profile->photo;

            DB::transaction(function () use ($user, $photoPath) {
                if ($photoPath && $photoPath !== 'default.png' && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
                $user->forceDelete();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('force deleted', 'user', [
                'user_id' => $userId,
                'user_full_name' => $fullName,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "User {$fullName} berhasil dihapus permanen.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('force delete', 'user', $e, [
                'user_id' => $id,
            ]);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat menghapus permanen user.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:0,1'
        ]);

        $newStatus = $request->boolean('status');
        $oldStatus = $user->status;
        $userName = $user->profile?->full_name ?? $user->email;

        if ($newStatus === $oldStatus) {
            $statusText = $newStatus ? 'Aktif' : 'Nonaktif';
            return redirect()->back()->with('notification_data', [
                'type' => 'info',
                'text' => "Status User {$userName} sudah {$statusText}.",
                'position' => 'center-top',
                'duration' => 3000,
            ]);
        }

        try {
            DB::transaction(function () use ($user, $newStatus) {
                $user->update([
                    'status' => $newStatus
                ]);
            });

            // LOG SUCCESS
            LogHelper::logSuccess('updated status', 'user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ], $request);

            $messageText = $newStatus
                ? "User {$userName} berhasil diaktifkan!"
                : "User {$userName} berhasil dinonaktifkan!";

            // Flash notification
            session()->flash('notification_data', [
                'type' => 'success',
                'text' => $messageText,
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update status', 'user', $e, [
                'user_id' => $user->id,
            ], $request);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat mengubah status user. Silakan coba lagi.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    public function getStudyPrograms()
    {
//        return StudyProgram::withoutTrashed()
//            ->select('id', 'degree', 'name')
//            ->orderBy('degree')
//            ->orderBy('name')
//            ->get()
//            ->mapWithKeys(fn($p) => [$p->id => $p->degree_name]);

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
