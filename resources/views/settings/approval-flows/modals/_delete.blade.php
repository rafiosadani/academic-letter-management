{{-- Delete Approval Flow Modal --}}
<form id="delete-approval-flow-form-{{ $flow->id }}"
      method="POST"
      action="{{ route('settings.approval-flows.destroy', $flow) }}"
      class="hidden">
    @csrf
    @method('DELETE')
</form>

<x-modal.confirm
        id="delete-approval-flow-modal-{{ $flow->id }}"
        title="⚠️ Hapus Step PERMANEN?"
        confirm-type="error"
        confirm-text="Ya, Hapus Permanen!"
        cancel-text="Batal"
        form="delete-approval-flow-form-{{ $flow->id }}"
>
    <x-slot:message>
        <div class="space-y-3">
            <p class="">Step ini akan <strong class="text-error">DIHAPUS PERMANEN</strong> dan <strong>TIDAK DAPAT DIKEMBALIKAN!</strong></p>

            {{-- Flow Info --}}
            <div class="rounded-lg bg-slate-100 dark:bg-navy-600 p-3">
                <p class="font-medium text-slate-700 dark:text-navy-100">Step {{ $flow->step }}: {{ $flow->step_label }}</p>
                <p class="text-slate-600 dark:text-navy-200 text-xs mt-1">Jenis: {{ $flow->letter_type->label() }}</p>
                @foreach($flow->required_positions as $position)
                    @php
                        $posEnum = App\Enums\OfficialPosition::from($position);
                    @endphp
                    <p class="badge bg-{{ $posEnum->color() }}/10 text-{{ $posEnum->color() }} text-tiny mt-1">
                        <i class="fa-solid text-tiny {{ $posEnum->icon() }} mr-1"></i>
                        {{ $posEnum->label() }}
                    </p>
                @endforeach
            </div>

            {{-- Consequences --}}
            <div class="rounded-lg bg-error/10 border border-error/20 p-3">
                <p class="font-medium text-error text-sm mb-2">
                    <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                    Konsekuensi:
                </p>
                <ul class="text-xs text-slate-600 dark:text-navy-200 space-y-1">
                    <li>• Step ini akan dihapus PERMANEN</li>
                    @php
                        $nextSteps = \App\Models\ApprovalFlow::where('letter_type', $flow->letter_type)
                            ->where('step', '>', $flow->step)
                            ->ordered()
                            ->get();
                    @endphp
                    @foreach($nextSteps as $nextStep)
                        <li>• Step {{ $nextStep->step }} → akan jadi Step {{ $nextStep->step - 1 }}</li>
                    @endforeach
                    @if($nextSteps->isEmpty())
                        <li>• Tidak ada step lain yang terpengaruh</li>
                    @endif
                </ul>
            </div>

            <p class="text-slate-600 dark:text-navy-200">
                Pastikan Anda <strong>benar-benar yakin</strong> sebelum melanjutkan.
            </p>
        </div>
    </x-slot:message>
</x-modal.confirm>