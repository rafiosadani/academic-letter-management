<form id="force-delete-study-program-form-{{ $studyProgram->id }}"
      method="POST"
      action="{{ route('master.study-programs.force-delete', $studyProgram->id) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="force-delete-study-program-modal-{{ $studyProgram->id }}"
        title="Konfirmasi Hapus Permanen"
        confirm-type="error"
        confirm-text="Ya, Hapus Permanen!"
        cancel-text="Batal"
        form="force-delete-study-program-form-{{ $studyProgram->id }}"
>
    <x-slot:message>
        <span class="font-semibold text-error">PERHATIAN!</span>
        <br>
        Anda akan menghapus <strong>PERMANEN</strong> Program Studi <strong>{{ $studyProgram->degree_name }}</strong>.
        <br>
        <span class="text-sm">Data tidak dapat dikembalikan lagi. Apakah Anda benar-benar yakin?</span>
    </x-slot:message>
</x-modal.confirm>