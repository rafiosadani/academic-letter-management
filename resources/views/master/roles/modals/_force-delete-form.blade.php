<form id="force-delete-role-form-{{ $role->id }}"
      method="POST"
      action="{{ route('master.roles.force-delete', $role->id) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="force-delete-role-modal-{{ $role->id }}"
        title="Konfirmasi Hapus Permanen"
        confirm-type="error"
        confirm-text="Ya, Hapus Permanen!"
        cancel-text="Batal"
        form="force-delete-role-form-{{ $role->id }}"
>
    <x-slot:message>
        <span class="font-semibold text-error">PERHATIAN!</span>
        <br>
        Anda akan menghapus <strong>PERMANEN</strong> Role <strong>{{ $role->name }}</strong>.
        <br>
        <span class="text-sm">Data tidak dapat dikembalikan lagi. Apakah Anda benar-benar yakin?</span>
    </x-slot:message>
</x-modal.confirm>