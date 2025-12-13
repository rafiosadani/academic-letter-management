{{-- Delete Config Modal --}}
<form id="delete-config-form-{{ $config->id }}"
      method="POST"
      action="{{ route('settings.letter-number-configs.destroy', $config) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="delete-config-modal-{{ $config->id }}"
        title="Hapus Konfigurasi Nomor Surat?"
        confirm-type="error"
        form="delete-config-form-{{ $config->id }}"
>
    <x-slot:message>
        <div class="space-y-3">
            <p class="text-sm">Anda yakin ingin menghapus konfigurasi ini?</p>

            {{-- Config Info --}}
            <div class="rounded-lg bg-slate-100 dark:bg-navy-600 p-3">
                <p class="font-medium text-slate-700 dark:text-navy-100">
                    {{ $config->letter_type->label() }}
                </p>
                <code class="text-xs text-slate-600 dark:text-navy-200 mt-1 block">
                    {{ $config->generatePreview(1) }}
                </code>
            </div>

            {{-- Warning --}}
            <div class="rounded-lg bg-warning/10 border border-warning/20 p-3">
                <p class="font-medium text-warning text-sm mb-2">
                    <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                    Peringatan:
                </p>
                <ul class="text-xs text-slate-600 dark:text-navy-200 space-y-1">
                    <li>• Konfigurasi akan dihapus permanen</li>
                    <li>• Counter tahun ini akan hilang</li>
                    <li>• Tidak bisa generate nomor surat untuk jenis ini</li>
                    <li>• Surat yang sudah ada tetap aman</li>
                </ul>
            </div>

            <p class="text-sm text-slate-600 dark:text-navy-200">
                Pastikan Anda benar-benar yakin sebelum melanjutkan.
            </p>
        </div>
    </x-slot:message>
</x-modal.confirm>