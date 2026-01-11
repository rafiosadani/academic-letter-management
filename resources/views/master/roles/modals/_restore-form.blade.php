<form id="restore-role-form-{{ $role->id }}"
      method="POST"
      action="{{ route('master.roles.restore', $role->id) }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="restore-role-modal-{{ $role->id }}"
        title="Konfirmasi Restore Role"
        message="Anda yakin ingin mengembalikan Role {{ $role->name }}? Role akan aktif kembali dalam sistem."
        confirm-type="success"
        confirm-text="Ya, Restore Role!"
        cancel-text="Batal"
        form="restore-role-form-{{ $role->id }}"
>
    <x-slot:message>
        Anda yakin ingin mengembalikan Role <strong>{{ $role->name }}</strong>?
        <br>
        Role akan aktif kembali dalam sistem.
    </x-slot:message>
</x-modal.confirm>