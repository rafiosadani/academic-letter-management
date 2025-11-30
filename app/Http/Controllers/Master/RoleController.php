<?php

namespace App\Http\Controllers\Master;

use App\Helpers\CodeGeneration;
use App\Helpers\LogHelper;
use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Role\CreateRoleRequest;
use App\Http\Requests\Master\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with(['createdByUser'])
            ->orderBy('code', 'desc');

        if ($request->has('view_deleted')) {
            $query->onlyTrashed();
        }

        $roles = $query->filter($request->only('search'))
            ->paginate(10)
            ->withQueryString();

        return view('master.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groupedPermissions = PermissionHelper::getGroupedPermissions();
        return view('master.roles.form', compact('groupedPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRoleRequest $request)
    {
        $role = null;
        try {
            DB::transaction(function () use ($request, &$role) {
                // Generate kode otomatis
                $code = (new CodeGeneration(Role::class, 'code', 'ROL'))->getGeneratedCode();

                // Buat role baru
                $role = Role::create([
                    'code' => $code,
                    'name' => $request->name,
                    'guard_name' => 'web',
                    'is_editable' => $request->is_editable,
                    'is_deletable' => $request->is_deletable,
                ]);

                // Sync permissions jika ada
                if ($request->filled('permissions')) {
                    $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
                    $role->syncPermissions($permissions);
                }
            });

            // LOG SUCCESS
            LogHelper::logSuccess('created', 'role', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions_count' => count($request->permissions ?? []),
            ], $request);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Data Role {$role->name} berhasil ditambahkan!",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.roles.index');
        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('create', 'role', $e, [
                'request_data' => $request->except(['_token'])
            ], $request);

            return redirect()->back()->withInput()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat menambahkan Role. Silakan coba lagi atau periksa input Anda.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        $groupedPermissions = PermissionHelper::getGroupedPermissions();

        return view('master.roles.show', compact('role', 'groupedPermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if (!$role->is_editable) {
            return redirect()
                ->route('master.roles.index')
                ->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Role ini tidak dapat diedit!',
                    'position' => 'center-top',
                    'duration' => 4000,
                ]);
        }

        $role->load('permissions');
        $groupedPermissions = PermissionHelper::getGroupedPermissions();

        return view('master.roles.form', compact('role', 'groupedPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        try {
            // Cek apakah role bisa diedit
            if (!$role->is_editable) {
                // LOG WARNING
                LogHelper::logWarning('Attempt to edit non-editable role', [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                ], $request);

//                // LOG WARNING (Bahasa Indonesia)
//                LogHelper::logWarning('Akses ditolak: Percobaan mengedit role yang tidak dapat diubah.', [
//                    'role_id' => $role->id,
//                    'role_name' => $role->name,
//                ], $request);
//
//                // LOG WARNING (English)
//                LogHelper::logWarning('Access denied: Attempt to edit non-editable role.', [
//                    'role_id' => $role->id,
//                    'role_name' => $role->name,
//                ], $request);

                return redirect()->back()->with('notification_data', [
                    'type' => 'error',
                    'text' => 'Role ini tidak dapat diedit!',
                    'position' => 'center-top',
                    'duration' => 4000,
                ]);
            }

            DB::transaction(function () use ($request, $role) {
                // Update role
                $role->update([
                    'name' => $request->name,
                    'is_editable' => $request->is_editable,
                    'is_deletable' => $request->is_deletable,
                ]);

                // Sync permissions
                if ($request->filled('permissions')) {
                    $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
                    $role->syncPermissions($permissions);
                } else {
                    // Jika tidak ada permissions, hapus semua
                    $role->syncPermissions([]);
                }
            });

            // LOG SUCCESS
            LogHelper::logSuccess('updated', 'role', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ], $request);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Data Role {$role->name} berhasil diperbarui!",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.roles.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('update', 'role', $e, [
                'role_id' => $role->id,
            ], $request);

            return redirect()->back()->withInput()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat memperbarui Role. Silakan coba lagi.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (!$role->is_deletable) {
            // LOG WARNING
            LogHelper::logWarning('Attempt to delete non-deletable role', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ]);

//            // LOG WARNING (Bahasa Indonesia)
//            LogHelper::logWarning('Akses ditolak: Percobaan menghapus role yang tidak dapat dihapus.', [
//                'role_id' => $role->id,
//                'role_name' => $role->name,
//            ]);
//
//            // LOG WARNING (English)
//            LogHelper::logWarning('Access denied: Attempt to delete non-deletable role.', [
//                'role_id' => $role->id,
//                'role_name' => $role->name,
//            ]);

            session()->flash('notification_data', [
                'type' => 'error',
                'text' => "Role {$role->name} dilindungi dan tidak dapat dihapus.",
                'position' => 'center-top',
                'duration' => 5000
            ]);
            return back();
        }

        try {
            $roleName = $role->name;
            DB::transaction(function () use ($role) {
                $role->delete();
            });

            // 3. Set Flash Session untuk menampilkan Modal Alert
//            session()->flash('alert_show_id', 'alert-role-delete-success');
//
//            // Flash untuk konfigurasi modal alert
//            session()->flash('modal_alert', [
//                'id'          => 'alert-role-delete-success',
//                'type'        => 'success',
//                'title'       => 'Role Terhapus',
//                'message'     => "Role {$roleName} berhasil dihapus.",
//                'buttonText'  => 'OK',
//                'showButton'  => true,
//            ]);

            // LOG SUCCESS
            LogHelper::logSuccess('deleted', 'role', [
                'role_id' => $role->id,
                'role_name' => $roleName,
            ]);

            // Opsional: Kirim data notifikasi toast
            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Data Role {$roleName} berhasil dihapus.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.roles.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('delete', 'role', $e, [
                'role_id' => $role->id,
            ]);

            return back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Gagal menghapus Role. Mungkin Role ini sedang digunakan oleh data lain.',
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
            $role = Role::onlyTrashed()->findOrFail($id);
            $roleName = $role->name;

            DB::transaction(function () use ($role) {
                // Restore - akan otomatis clear deleted_by via RecordSignature trait
                $role->restore();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('restored', 'role', [
                'role_id' => $role->id,
                'role_name' => $roleName,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Role {$roleName} berhasil direstore.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('restore', 'role', $e, [
                'role_id' => $id,
            ]);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat restore role.',
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
            $count = Role::onlyTrashed()->count();

            if ($count === 0) {
                session()->flash('notification_data', [
                    'type' => 'info',
                    'text' => 'Tidak ada role yang perlu direstore.',
                    'position' => 'center-top',
                    'duration' => 4000
                ]);

                return redirect()->back();
            }

            DB::transaction(function () {
                Role::onlyTrashed()->restore();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('restored all', 'role', [
                'restored_count' => $count,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Berhasil restore {$count} role.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->route('master.roles.index');

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('restore all', 'role', $e);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat restore semua role.',
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
            $role = Role::onlyTrashed()->findOrFail($id);

            if (!$role->is_deletable) {
                // LOG WARNING
                LogHelper::logWarning('Attempt to force delete non-deletable role', [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                ]);

                session()->flash('notification_data', [
                    'type' => 'error',
                    'text' => "Role {$role->name} dilindungi dan tidak dapat dihapus permanen.",
                    'position' => 'center-top',
                    'duration' => 5000
                ]);

                return redirect()->back();
            }

            $roleName = $role->name;
            $roleId = $role->id;

            DB::transaction(function () use ($role) {
                // Permanent delete
                $role->forceDelete();
            });

            // LOG SUCCESS
            LogHelper::logSuccess('force deleted', 'role', [
                'role_id' => $roleId,
                'role_name' => $roleName,
            ]);

            session()->flash('notification_data', [
                'type' => 'success',
                'text' => "Role {$roleName} berhasil dihapus permanen.",
                'position' => 'center-top',
                'duration' => 4000
            ]);

            return redirect()->back();

        } catch (\Exception $e) {
            // LOG ERROR
            LogHelper::logError('force delete', 'role', $e, [
                'role_id' => $id,
            ]);

            return redirect()->back()->with('notification_data', [
                'type' => 'error',
                'text' => 'Terjadi kesalahan saat menghapus permanen role.',
                'position' => 'center-top',
                'duration' => 6000,
            ]);
        }
    }
}
