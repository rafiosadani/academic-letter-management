<form id="force-delete-user-form-{{ $user->id }}"
      method="POST"
      action="{{ route('master.users.force-delete', $user->id) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="force-delete-user-modal-{{ $user->id }}"
        title="Konfirmasi Hapus Permanen"
        confirm-type="error"
        confirm-text="Ya, Hapus Permanen!"
        cancel-text="Batal"
        form="force-delete-user-form-{{ $user->id }}"
>
    <x-slot:message>
        <span class="font-semibold text-error">PERHATIAN!</span>
        <br>
        Anda akan menghapus <strong>PERMANEN</strong> User <strong>{{ $user->profile->full_name }}</strong>.
        <br>
        <span class="text-sm">Data tidak dapat dikembalikan lagi. Apakah Anda benar-benar yakin?</span>
    </x-slot:message>
</x-modal.confirm>