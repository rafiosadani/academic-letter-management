<form id="delete-letter-form-{{ $letter->id }}"
      method="POST"
      action="{{ route('letters.destroy', $letter) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="delete-letter-modal-{{ $letter->id }}"
        title="⚠️ Batalkan Pengajuan Surat?"
        confirm-type="error"
        confirm-text="Ya, Batalkan!"
        cancel-text="Batal"
        form="delete-letter-form-{{ $letter->id }}"
>
    <x-slot:message>
        <div class="space-y-3">
            <p class="">Pengajuan surat ini akan <strong class="text-error">DIBATALKAN</strong> dan <strong>DIHAPUS dari daftar Anda</strong>.</p>

            {{-- Letter Info --}}
            <div class="rounded-lg bg-slate-100 dark:bg-navy-600 p-3">
                <p class="font-medium text-slate-700 dark:text-navy-100">{{ $letter->letter_type->label() }}</p>
                <p class="text-slate-600 dark:text-navy-200 text-xs mt-1">
                    Diajukan: {{ $letter->created_at->translatedFormat('d F Y, H:i') }} WIB
                </p>
                <p class="text-slate-600 dark:text-navy-200 text-xs mt-1">
                    Semester {{ $letter->semester?->semester_type }} - Tahun Akademik {{ $letter->academicYear?->year_label }}
                </p>
                <div class="mt-2">
                    <span class="badge bg-{{ $letter->status_badge }}/10 text-{{ $letter->status_badge }} text-[9px] border border-{{ $letter->status_badge }} inline-flex items-center space-x-1.5 dark:bg-{{ $letter->status_badge }}/15">
                        <i class="{{ $letter->status_icon }} {{ in_array($letter->status, ['in_progress','external_processing']) ? 'animate-spin' : '' }}"></i>
                        <span>{{ $letter->status_label }}</span>
                    </span>
                </div>
            </div>

            {{-- Consequences --}}
            <div class="rounded-lg bg-warning/10 border border-warning/20 p-3">
                <p class="font-medium text-warning text-sm mb-2">
                    <i class="fa-solid fa-info-circle mr-1"></i>
                    Yang Akan Terjadi:
                </p>
                <ul class="text-xs text-slate-600 dark:text-navy-200 space-y-1">
                    <li>• Pengajuan akan dihapus dari daftar Anda</li>
                    @if($letter->documents->count() > 0)
                        <li>• Dokumen pendukung ({{ $letter->documents->count() }} file) akan disembunyikan</li>
                    @endif
                    @if($letter->approvals->count() > 0)
                        <li>• Riwayat approval akan disimpan untuk audit</li>
                    @endif
                    <li>• Data disimpan di sistem untuk keperluan audit</li>
                    <li>• Pengajuan ini tidak dapat dikembalikan lagi</li>
                </ul>
            </div>

            <p class="text-slate-600 dark:text-navy-200 text-xs">
                <i class="fa-solid fa-lightbulb text-info mr-1"></i>
                <strong>Tip:</strong> Jika hanya ingin memperbaiki data, gunakan tombol <strong>Edit</strong> (jika tersedia).
            </p>
        </div>
    </x-slot:message>
</x-modal.confirm>