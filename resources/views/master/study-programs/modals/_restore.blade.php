<form id="restore-study-program-form-{{ $studyProgram->id }}"
      method="POST"
      action="{{ route('master.study-programs.restore', $studyProgram->id) }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="restore-study-program-modal-{{ $studyProgram->id }}"
        title="Konfirmasi Restore Program Studi"
        confirm-type="success"
        confirm-text="Ya, Restore Program Studi!"
        cancel-text="Batal"
        form="restore-study-program-form-{{ $studyProgram->id }}"
>
    <x-slot:message>
        Anda yakin ingin mengembalikan Program Studi <strong>{{ $studyProgram->degree_name }}</strong>?
        <br>
        Program studi akan aktif kembali dalam sistem.
    </x-slot:message>
</x-modal.confirm>