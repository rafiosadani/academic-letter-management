<form id="restore-user-form-{{ $user->id }}"
      method="POST"
      action="{{ route('master.users.restore', $user->id) }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="restore-user-modal-{{ $user->id }}"
        title="Konfirmasi Restore User"
        confirm-type="success"
        confirm-text="Ya, Restore User!"
        cancel-text="Batal"
        form="restore-user-form-{{ $user->id }}"
>
    <x-slot:message>
        Anda yakin ingin mengembalikan User <strong>{{ $user->profile?->full_name }}</strong>?
        <br>
        User akan aktif kembali dalam sistem.
    </x-slot:message>
</x-modal.confirm>