<form id="delete-role-form-{{ $role->id }}"
      method="POST"
      action="{{ route('master.roles.destroy', $role->id) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="delete-role-modal-{{ $role->id }}"
        title="Konfirmasi Hapus Role"
        confirm-type="warning"
        confirm-text="Ya, Hapus Role!"
        cancel-text="Batal"
        form="delete-role-form-{{ $role->id }}"
>
    <x-slot:message>
        Anda yakin ingin menghapus Role <strong>{{ $role->name }}</strong>?
        <br>
        Data akan dipindahkan ke trash dan masih dapat direstore kembali.
    </x-slot:message>
</x-modal.confirm>