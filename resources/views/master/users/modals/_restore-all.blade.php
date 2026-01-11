<form id="restore-all-users-form"
      method="POST"
      action="{{ route('master.users.restore.all') }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="restore-all-users-modal"
        title="Konfirmasi Restore Semua User"
        confirm-type="success"
        confirm-text="Ya, Restore Semua!"
        cancel-text="Batal"
        form="restore-all-users-form"
>
    <x-slot:message>
        Anda yakin ingin mengembalikan <strong class="text-success">{{ $users->total() }} user</strong> yang terhapus?
        <br>
        Semua user akan aktif kembali dalam sistem.
    </x-slot:message>
</x-modal.confirm>