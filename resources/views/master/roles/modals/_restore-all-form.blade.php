<form id="restore-all-roles-form"
      method="POST"
      action="{{ route('master.roles.restore.all') }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="restore-all-roles-modal"
        title="Konfirmasi Restore Semua Role"
        confirm-type="success"
        confirm-text="Ya, Restore Semua!"
        cancel-text="Batal"
        form="restore-all-roles-form"
>
    <x-slot:message>
        Anda yakin ingin mengembalikan <strong class="text-success">{{ $roles->total() }} role</strong> yang terhapus?
        <br>
        Semua role akan aktif kembali dalam sistem.
    </x-slot:message>
</x-modal.confirm>