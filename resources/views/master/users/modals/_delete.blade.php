<form id="delete-user-form-{{ $user->id }}"
      method="POST"
      action="{{ route('master.users.destroy', $user->id) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="delete-user-modal-{{ $user->id }}"
        title="Konfirmasi Hapus User"
        confirm-type="warning"
        confirm-text="Ya, Hapus User!"
        cancel-text="Batal"
        form="delete-user-form-{{ $user->id }}"
>
    <x-slot:message>
        Anda yakin ingin menghapus User <strong>{{ $user->profile->full_name }}</strong>?
        <br>
        Data akan dipindahkan ke trash dan masih dapat direstore kembali.
    </x-slot:message>
</x-modal.confirm>