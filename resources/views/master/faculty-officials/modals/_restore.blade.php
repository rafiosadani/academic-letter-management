<form id="restore-faculty-official-form-{{ $official->id }}"
      method="POST"
      action="{{ route('master.faculty-officials.restore', $official->id) }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="restore-faculty-official-modal-{{ $official->id }}"
        title="Konfirmasi Restore Penugasan Jabatan"
        confirm-type="success"
        confirm-text="Ya, Restore!"
        cancel-text="Batal"
        form="restore-faculty-official-form-{{ $official->id }}"
>
    <x-slot:message>
        Anda yakin ingin mengembalikan penugasan jabatan:
        <br>
        <strong>{{ $official->user->profile->full_name ?? $official->user->email }}</strong> - <strong>{{ $official->position->label() }}</strong>
        @if($official->studyProgram)
            <br>
            Program Studi: <strong>{{ $official->studyProgram->degree_name }}</strong>
        @endif
        <br>
        <span class="text-xs">Periode: {{ $official->period }}</span>
        <br><br>
        <div class="text-xs bg-info/10 border border-info/20 rounded p-2 text-slate-600 dark:text-navy-200">
            <i class="fa-solid fa-info-circle text-info mr-1"></i>
            Data akan aktif kembali dalam sistem.
        </div>
    </x-slot:message>
</x-modal.confirm>