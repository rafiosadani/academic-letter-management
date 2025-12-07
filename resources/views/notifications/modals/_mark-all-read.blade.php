<form id="mark-all-notifications-read-form" class="hidden">
    @csrf
</form>

<x-modal.confirm
    id="mark-all-notifications-read-modal"
    title="Konfirmasi Tandai Semua Dibaca"
    confirm-type="success"
    confirm-text="Ya, Tandai Semua!"
    cancel-text="Batal"
    form="mark-all-notifications-read-form"
>
    <x-slot:message>
        Anda yakin ingin menandai <strong class="text-success">{{ $unreadCount }} notifikasi</strong> sebagai sudah dibaca?
    </x-slot:message>
</x-modal.confirm>