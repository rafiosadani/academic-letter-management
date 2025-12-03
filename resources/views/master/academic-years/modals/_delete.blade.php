<form id="delete-academic-year-form-{{ $academicYear->id }}"
      method="POST"
      action="{{ route('master.academic-years.destroy', $academicYear->id) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="delete-academic-year-modal-{{ $academicYear->id }}"
        title="Konfirmasi Hapus Tahun Akademik"
        confirm-type="error"
        confirm-text="Ya, Hapus Permanen!"
        cancel-text="Batal"
        form="delete-academic-year-form-{{ $academicYear->id }}"
>
    <x-slot:message>
        <span class="font-semibold text-error">PERHATIAN!</span>
        <br>
        Anda akan menghapus <strong>PERMANEN</strong> Tahun Akademik <strong>{{ $academicYear->year_label }}</strong>.
        <br>
        <span class="text-sm">Data tidak dapat dikembalikan lagi. Apakah Anda benar-benar yakin?</span>
        <br><br>
        <span class="text-xs text-error">
            <i class="fa-solid fa-exclamation-triangle mr-1"></i>
            Semester terkait juga akan ikut dihapus permanen!
        </span>
    </x-slot:message>
</x-modal.confirm>