<form id="update-status-form-{{ $user->id }}"
      method="POST"
      action="{{ route('master.users.updateStatus', $user->id) }}"
      class="hidden">
    @csrf
    @method('PATCH')

    {{-- Hidden input yang akan diupdate oleh JS --}}
    <input type="hidden" name="status" value="{{ $user->status ? '0' : '1' }}">
</form>

<x-modal.confirm
        id="confirm-update-status-modal-{{ $user->id }}"
        title="Konfirmasi Perubahan Status"
        confirm-type="{{ $user->status ? 'warning' : 'success' }}"
        confirm-text="{{ $user->status ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
        cancel-text="Batal"
        form="update-status-form-{{ $user->id }}"
>
    <x-slot:message>
        @if ($user->status)
            Status akun <strong>{{ $user->profile->full_name ?? $user->email }}</strong> akan diubah menjadi
            <span class="font-semibold text-warning">Nonaktif</span>.
            <br>
            Pengguna tidak akan bisa login sampai status diaktifkan kembali.
        @else
            Status akun <strong>{{ $user->profile->full_name ?? $user->email }}</strong> akan diubah menjadi
            <span class="font-semibold text-success">Aktif</span>.
            <br>
            Pengguna akan dapat login dan mengakses sistem.
        @endif
    </x-slot:message>
</x-modal.confirm>
