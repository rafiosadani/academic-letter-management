<x-modal.form
        id="edit-role-modal-{{ $role->id }}"
        title="Edit Role: {{ $role->name }}"
        data-open-on-error="{{ ($errors->any() && old('is_edit_form') == $role->id) ? 'true' : 'false' }}"
        action="{{ route('master.roles.update', $role->id) }}"
        method="PUT"
        submit-text="Update Role"
        cancel-text="Batal"
        size="7xl"
>
    <input type="hidden" name="is_edit_form" value="{{ $role->id }}">
    <div class="role-permission-form">
        @include('master.roles._form', [
            'role' => $role,
            'groupedPermissions' => $groupedPermissions ?? [],
            'rolePermissionIds' => ($errors->any() && old('is_edit_form') == $role->id)
                ? old('permissions', [])
                : $role->permissions->pluck('id')->toArray()
        ])
    </div>
</x-modal.form>