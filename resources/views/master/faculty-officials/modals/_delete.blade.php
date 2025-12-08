<form id="delete-faculty-official-form-{{ $official->id }}"
      method="POST"
      action="{{ route('master.faculty-officials.destroy', $official->id) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="delete-faculty-official-modal-{{ $official->id }}"
        title="Konfirmasi Hapus Penugasan Jabatan"
        confirm-type="warning"
        confirm-text="Ya, Hapus!"
        cancel-text="Batal"
        form="delete-faculty-official-form-{{ $official->id }}"
>
    <x-slot:message>
        Anda yakin ingin menghapus penugasan jabatan:
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
            Data akan dipindahkan ke trash dan masih dapat direstore kembali.
        </div>
    </x-slot:message>
</x-modal.confirm>