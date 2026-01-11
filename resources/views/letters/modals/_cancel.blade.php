<form id="cancel-letter-form-{{ $letter->id }}"
      method="POST"
      action="{{ route('letters.cancel', $letter) }}"
      class="hidden">
    @csrf
</form>

<x-modal.confirm
        id="cancel-letter-modal-{{ $letter->id }}"
        title="⚠️ Batalkan Pengajuan Surat?"
        confirm-type="warning"
        confirm-text="Ya, Batalkan"
        cancel-text="Tidak"
        form="cancel-letter-form-{{ $letter->id }}"
>
    <x-slot:message>
        <div class="space-y-3">
            <p>Anda akan <strong class="text-warning">MEMBATALKAN</strong> pengajuan surat ini.</p>

            {{-- Letter Info --}}
            <div class="rounded-lg bg-slate-100 dark:bg-navy-600 p-3">
                <p class="font-medium text-slate-700 dark:text-navy-100">{{ $letter->letter_type->label() }}</p>
                <p class="text-slate-600 dark:text-navy-200 text-xs mt-1">
                    Diajukan: {{ $letter->created_at->translatedFormat('d F Y, H:i') }} WIB
                </p>
                <div class="mt-2">
                    <span class="badge bg-{{ $letter->status_badge }}/10 text-{{ $letter->status_badge }} text-[9px] border border-{{ $letter->status_badge }} inline-flex items-center space-x-1.5 dark:bg-{{ $letter->status_badge }}/15">
                        <i class="{{ $letter->status_icon }} {{ in_array($letter->status, ['in_progress','external_processing']) ? 'animate-spin' : '' }}"></i>
                        <span>{{ $letter->status_label }}</span>
                    </span>
                </div>
            </div>

            {{-- Current Progress --}}
            @if($letter->currentApproval)
                <div class="rounded-lg bg-info/10 border border-info/20 p-3">
                    <p class="font-medium text-info text-sm mb-1">
                        <i class="fa-solid fa-info-circle mr-1"></i>
                        Progress Saat Ini:
                    </p>
                    <p class="text-xs text-slate-600 dark:text-navy-200">
                        {{ $letter->currentApproval->step_label }}
                    </p>
                </div>
            @endif

            {{-- Warning --}}
            <div class="rounded-lg bg-warning/10 border border-warning/20 p-3">
                <p class="font-medium text-warning text-sm mb-2">
                    <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                    Yang Akan Terjadi:
                </p>
                <ul class="text-xs text-slate-600 dark:text-navy-200 space-y-1">
                    <li>• Proses approval akan dihentikan</li>
                    <li>• Status berubah menjadi "Dibatalkan"</li>
                    <li>• Pengajuan tidak dapat dilanjutkan kembali</li>
                    <li>• Anda bisa mengajukan surat baru jika diperlukan</li>
                </ul>
            </div>

            <p class="text-slate-600 dark:text-navy-200 text-xs">
                <i class="fa-solid fa-lightbulb text-info mr-1"></i>
                <strong>Tip:</strong> Jika hanya perlu perbaikan data, gunakan tombol <strong>Edit</strong> (jika tersedia).
            </p>
        </div>
    </x-slot:message>
</x-modal.confirm>