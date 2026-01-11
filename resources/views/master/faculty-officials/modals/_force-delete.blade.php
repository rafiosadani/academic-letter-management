<form id="force-delete-faculty-official-form-{{ $official->id }}"
      method="POST"
      action="{{ route('master.faculty-officials.force-delete', $official->id) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="force-delete-faculty-official-modal-{{ $official->id }}"
        title="Konfirmasi Hapus Permanen"
        confirm-type="error"
        confirm-text="Ya, Hapus Permanen!"
        cancel-text="Batal"
        form="force-delete-faculty-official-form-{{ $official->id }}"
>
    <x-slot:message>
        <span class="font-semibold text-error">PERHATIAN!</span>
        <br>
        Anda akan menghapus <strong>PERMANEN</strong> penugasan jabatan:
        <br>
        <strong>{{ $official->user->profile->full_name ?? $official->user->email }}</strong> - <strong>{{ $official->position->label() }}</strong>
        @if($official->studyProgram)
            <br>
            Program Studi: <strong>{{ $official->studyProgram->degree_name }}</strong>
        @endif
        <br>
        <span class="text-xs">Periode: {{ $official->period }}</span>
        <br><br>
        <div class="text-xs bg-error/10 border border-error/20 rounded p-2 text-slate-600 dark:text-navy-200">
            <i class="fa-solid fa-warning text-error mr-1"></i>
            Data tidak dapat dikembalikan lagi. Apakah Anda benar-benar yakin?
        </div>
    </x-slot:message>
</x-modal.confirm>