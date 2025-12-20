<form id="reject-form-{{ $approval->id }}"
      method="POST"
      action="{{ route('approvals.reject', $approval) }}"
      class="hidden">
    @csrf
    <input type="hidden" name="reason" id="reject-reason-{{ $approval->id }}" value="{{ old('reason') }}">
</form>

<x-modal.confirm
    id="reject-modal-{{ $approval->id }}"
    title="⚠️ Tolak Pengajuan Surat?"
    confirm-type="error"
    confirm-text="Ya, Tolak!"
    cancel-text="Batal"
    form="reject-form-{{ $approval->id }}"
    data-open-on-error="{{ session('open_reject_modal_id') == $approval->id ? 'true' : 'false' }}"
>
    <x-slot:message>
        <div class="space-y-3">
            <div class="flex items-start gap-3 rounded-lg bg-error/10 border border-error/20 p-3">
                <p class="text-sm text-slate-700 dark:text-navy-100">
                    Anda akan <strong class="text-error">MENOLAK</strong> pengajuan surat ini.
                    Pastikan alasan penolakan ditulis dengan jelas.
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
                    <div class="min-w-0 text-center">
                        <p class="font-medium text-slate-700 dark:text-navy-100 text-xs-plus truncate">
                            {{ $letter->student->profile->full_name }}
                        </p>
                        <p class="text-xs text-slate-600 dark:text-navy-200">
                            {{ $letter->student->profile->student_or_employee_id }}
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

            {{-- Action After Reject --}}
            @php
                $onReject = $approval->on_reject ?? \App\Enums\ApprovalAction::TO_STUDENT;
            @endphp

            <div class="rounded-lg bg-warning/10 border border-warning/20 p-3">
                <p class="font-medium text-warning text-sm mb-2">
                    <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                    Yang Akan Terjadi:
                </p>
                @if($onReject === \App\Enums\ApprovalAction::TO_STUDENT)
                    <ul class="text-xs text-slate-600 dark:text-navy-200 space-y-1">
                        <li>• Surat dikembalikan ke mahasiswa untuk perbaikan</li>
                        <li>• Mahasiswa dapat mengedit dan mengajukan ulang</li>
                        <li>• Proses approval akan dimulai dari awal</li>
                    </ul>
                @elseif($onReject === \App\Enums\ApprovalAction::TO_PREVIOUS_STEP)
                    <ul class="text-xs text-slate-600 dark:text-navy-200 space-y-1">
                        <li>• Surat kembali ke step approval sebelumnya</li>
                        <li>• Approver sebelumnya dapat mereview ulang</li>
                        <li>• Proses dilanjutkan dari step sebelumnya</li>
                    </ul>
                @else
                    <ul class="text-xs text-slate-600 dark:text-navy-200 space-y-1">
                        <li>• Surat ditolak secara permanen</li>
                        <li>• Mahasiswa harus membuat pengajuan baru</li>
                        <li>• Proses approval dihentikan total</li>
                    </ul>
                @endif
            </div>

            {{-- Required Reason--}}
            <div>
                <label class="block">
                    <span class="text-xs font-medium text-slate-600 dark:text-navy-100">
                        Alasan Penolakan <span class="text-error">*</span>
                    </span>
                    <textarea
                        id="reason-input-{{ $approval->id }}"
                        rows="3"
                        required
                        class="form-textarea mt-1.5 w-full rounded-lg text-xs border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                        placeholder="Jelaskan alasan penolakan secara detail..."
                        oninput="document.getElementById('reject-reason-{{ $approval->id }}').value = this.value"
                    ></textarea>
                </label>
                @if(!$errors->has('reason'))
                    <p class="text-tiny text-slate-400 dark:text-navy-300 mt-1">
                        Alasan ini akan dikirimkan ke mahasiswa
                    </p>
                @endif
                @error('reason')
                    <span class="text-tiny text-error mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </x-slot:message>
</x-modal.confirm>
