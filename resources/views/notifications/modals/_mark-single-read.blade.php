<form id="mark-notification-read-form-{{ $notification->id }}" class="hidden">
    @csrf
</form>

<x-modal.confirm
    id="mark-notification-read-modal-{{ $notification->id }}"
    title="Tandai Notifikasi Dibaca"
    confirm-type="success"
    confirm-text="Tandai Dibaca"
    cancel-text="Batal"
    form="mark-notification-read-form-{{ $notification->id }}"
>
    <x-slot:message>
        Tandai notifikasi ini sebagai sudah dibaca?
    </x-slot:message>
</x-modal.confirm>