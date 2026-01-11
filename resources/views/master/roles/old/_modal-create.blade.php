<x-modal.form
        id="create-role-modal"
        title="Tambah Role Baru"
        data-open-on-error="{{ ($errors->any() && old('is_create_form')) ? 'true' : 'false' }}"
        action="{{ route('master.roles.store') }}"
        method="POST"
        submit-text="Simpan Role"
        cancel-text="Batal"
        size="7xl"
>
    <input type="hidden" name="is_create_form" value="1">
    <div class="role-permission-form">
        @include('master.roles.old._form', [
            'role' => null,
            'groupedPermissions' => $groupedPermissions ?? [],
            'rolePermissionIds' => ($errors->any() && old('is_create_form')) ? old('permissions', []) : []
        ])
    </div>
</x-modal.form>