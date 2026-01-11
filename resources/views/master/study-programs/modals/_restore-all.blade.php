<form id="restore-all-study-programs-form"
      method="POST"
      action="{{ route('master.study-programs.restore.all') }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="restore-all-study-programs-modal"
        title="Konfirmasi Restore Semua Program Studi"
        confirm-type="success"
        confirm-text="Ya, Restore Semua!"
        cancel-text="Batal"
        form="restore-all-study-programs-form"
>
    <x-slot:message>
        Anda yakin ingin mengembalikan <strong class="text-success">{{ $studyPrograms->total() }} program studi</strong> yang terhapus?
        <br>
        Semua program studi akan aktif kembali dalam sistem.
    </x-slot:message>
</x-modal.confirm>