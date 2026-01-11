<form id="delete-study-program-form-{{ $studyProgram->id }}"
      method="POST"
      action="{{ route('master.study-programs.destroy', $studyProgram->id) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="delete-study-program-modal-{{ $studyProgram->id }}"
        title="Konfirmasi Hapus Program Studi"
        confirm-type="warning"
        confirm-text="Ya, Hapus Program Studi!"
        cancel-text="Batal"
        form="delete-study-program-form-{{ $studyProgram->id }}"
>
    <x-slot:message>
        Anda yakin ingin menghapus Program Studi <strong>{{ $studyProgram->degree_name }}</strong>?
        <br>
        Data akan dipindahkan ke trash dan masih dapat direstore kembali.
    </x-slot:message>
</x-modal.confirm>