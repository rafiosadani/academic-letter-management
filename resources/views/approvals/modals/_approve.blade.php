<form id="approve-form-{{ $approval->id }}"
      method="POST"
      action="{{ route('approvals.approve', $approval) }}"
      class="hidden">
    @csrf
    <input type="hidden" name="note" id="approve-note-{{ $approval->id }}">
</form>

<x-modal.confirm
        id="approve-modal-{{ $approval->id }}"
        title="Setujui Pengajuan Surat?"
        confirm-type="success"
        confirm-text="Ya, Setujui!"
        cancel-text="Batal"
        form="approve-form-{{ $approval->id }}"
>
    <x-slot:message>
        <div class="space-y-3">
            <div class="flex items-start gap-3 rounded-lg bg-success/10 border border-success/20 p-3">
                <p class="text-sm text-slate-700 dark:text-navy-100">
                    Anda akan <strong class="text-success">MENYETUJUI</strong> pengajuan surat ini.
                    Pastikan data sudah benar sebelum melanjutkan.
                </p>
            </div>
            <div class="rounded-lg bg-slate-100 dark:bg-navy-600 p-3">
                <div class="flex flex-col items-center gap-1.5 mb-2">
                    <div class="avatar size-7">
                        <img
                            src="{{ $letter->student->profile->photo_url ?? asset('images/default-avatar.png') }}"
                            alt="{{ $letter->student->profile->full_name }}"
                            class="rounded-full object-cover"
                        >
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-slate-700 dark:text-navy-100 text-xs-plus truncate">
                            {{ $letter->student->profile->full_name }}
                        </p>
                        <p class="text-xs text-slate-600 dark:text-navy-200">
                            {{ $letter->student->profile->student_or_employee_id ?? '-' }}
                        </p>
                        <p class="text-xs text-slate-600 dark:text-navy-200">
                            {{ $letter->student->profile->studyProgram->degree_name ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-navy-500 pt-2 mt-2">
                    <p class="font-medium text-slate-700 dark:text-navy-100 text-sm">
                        {{ $letter->letter_type->label() }}
                    </p>
                    <p class="text-xs text-slate-600 dark:text-navy-200 mt-1">
                        Step {{ $approval->step }}: {{ $approval->step_label }}
                    </p>
                </div>
            </div>

            @php
                $nextApproval = $letter->approvals->where('step', '>', $approval->step)->first();
            @endphp

            <div class="rounded-lg bg-info/10 border border-info/20 p-3">
                <p class="font-medium text-info text-sm mb-2">
                    <i class="fa-solid fa-arrow-right mr-1"></i>
                    Setelah Disetujui:
                </p>
                @if($approval->is_final)
                    <p class="text-xs text-slate-600 dark:text-navy-200">
                        @if($letter->letter_type->isExternal())
                            • Surat akan menunggu proses eksternal (SKAK dari UB Pusat)
                        @else
                            • Surat akan disetujui dan mendapatkan nomor surat
                            • Mahasiswa dapat mengunduh surat
                        @endif
                    </p>
                @elseif($nextApproval)
                    <p class="text-xs text-slate-600 dark:text-navy-200">
                        • Surat akan dilanjutkan ke step berikutnya
                    </p>
                    <p class="text-xs text-slate-600 dark:text-navy-200">
                        • <strong>{{ $nextApproval->step_label }}</strong>
                    </p>
                @endif
            </div>

            <div>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600 dark:text-navy-100">
                        Catatan (Opsional)
                    </span>
                    <textarea
                        id="note-input-{{ $approval->id }}"
                        rows="2"
                        class="form-textarea mt-1.5 w-full rounded-lg text-xs border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                        placeholder="Tambahkan catatan jika diperlukan..."
                        onchange="document.getElementById('approve-note-{{ $approval->id }}').value = this.value"
                    ></textarea>
                </label>
            </div>
        </div>
    </x-slot:message>
</x-modal.confirm>
