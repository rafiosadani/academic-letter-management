<form id="restore-all-faculty-officials-form"
      method="POST"
      action="{{ route('master.faculty-officials.restore.all') }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="restore-all-faculty-officials-modal"
        title="Konfirmasi Restore Semua Penugasan Jabatan"
        confirm-type="success"
        confirm-text="Ya, Restore Semua!"
        cancel-text="Batal"
        form="restore-all-faculty-officials-form"
>
    <x-slot:message>
        Anda yakin ingin mengembalikan <strong class="text-success">{{ $facultyOfficials->total() }} penugasan jabatan</strong> yang terhapus?
        <br><br>
        <div class="text-xs bg-info/10 border border-info/20 rounded p-2 text-slate-600 dark:text-navy-200">
            <i class="fa-solid fa-info-circle text-info mr-1"></i>
            Semua data akan aktif kembali dalam sistem.
        </div>
    </x-slot:message>
</x-modal.confirm>