<form id="toggle-active-semester-form-{{ $semester->id }}"
      method="POST"
      action="{{ route('master.semesters.toggle-active', $semester->id) }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="toggle-active-semester-modal-{{ $semester->id }}"
        title="Konfirmasi Aktifkan Semester"
        confirm-type="success"
        confirm-text="Ya, Aktifkan Semester!"
        cancel-text="Batal"
        form="toggle-active-semester-form-{{ $semester->id }}"
>
    <x-slot:message>
        Anda yakin ingin mengaktifkan <strong>Semester {{ $semester->semester_type->label() }} {{ $semester->academicYear->year_label }}</strong>?
        <br><br>
        <div class="text-xs bg-info/10 border border-info/20 rounded p-2 text-slate-600 dark:text-navy-200">
            <i class="fa-solid fa-info-circle text-info mr-1"></i>
            Semester lain yang sedang aktif akan otomatis dinonaktifkan.
        </div>
    </x-slot:message>
</x-modal.confirm>