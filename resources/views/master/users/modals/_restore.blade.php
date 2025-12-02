<form id="restore-user-form-{{ $user->id }}"
      method="POST"
      action="{{ route('master.users.restore', $user->id) }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="restore-user-modal-{{ $user->id }}"
        title="Konfirmasi Restore User"
        message="Anda yakin ingin mengembalikan User {{ $user->profile->full_name }}? user akan aktif kembali dalam sistem."
        confirm-type="success"
        confirm-text="Ya, Restore User!"
        cancel-text="Batal"
        form="restore-user-form-{{ $user->id }}"
/>