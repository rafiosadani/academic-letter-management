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
                <p class="font-bold text-warning text-sm mb-2 flex items-center justify-center text-center">
                    <i class="fa-solid fa-circle-exclamation mr-2 text-xs"></i>
                    YANG AKAN TERJADI
                </p>

                <div class="flex flex-col items-center space-y-1">
                    @if($onReject === \App\Enums\ApprovalAction::TO_STUDENT)
                        <div class="flex items-center text-left max-w-xs w-full sm:w-auto">
                            <i class="fa-solid fa-user-pen text-warning mr-2 text-xs shrink-0"></i>
                            <p class="text-xs text-slate-600 dark:text-navy-200">
                                Surat dikembalikan ke mahasiswa untuk perbaikan
                            </p>
                        </div>
                        <div class="flex items-center text-left max-w-xs w-full sm:w-auto">
                            <i class="fa-solid fa-rotate-left text-warning mr-2 text-xs shrink-0"></i>
                            <p class="text-xs text-slate-600 dark:text-navy-200">
                                Mahasiswa dapat mengedit dan mengajukan ulang
                            </p>
                        </div>
                        <div class="flex items-center text-left max-w-xs w-full sm:w-auto">
                            <i class="fa-solid fa-forward-step text-warning mr-2 text-xs shrink-0"></i>
                            <p class="text-xs text-slate-600 dark:text-navy-200">
                                Proses approval akan dimulai dari awal
                            </p>
                        </div>

                    @elseif($onReject === \App\Enums\ApprovalAction::TO_PREVIOUS_STEP)
                        <div class="flex items-center text-left max-w-xs w-full sm:w-auto">
                            <i class="fa-solid fa-backward text-warning mr-2 text-xs shrink-0"></i>
                            <p class="text-xs text-slate-600 dark:text-navy-200">
                                Surat kembali ke tahap approval sebelumnya
                            </p>
                        </div>
                        <div class="flex items-center text-left max-w-xs w-full sm:w-auto">
                            <i class="fa-solid fa-magnifying-glass text-warning mr-2 text-xs shrink-0"></i>
                            <p class="text-xs text-slate-600 dark:text-navy-200">
                                Approver sebelumnya dapat mereview ulang
                            </p>
                        </div>
                        <div class="flex items-center text-left max-w-xs w-full sm:w-auto">
                            <i class="fa-solid fa-play text-warning mr-2 text-xs shrink-0"></i>
                            <p class="text-xs text-slate-600 dark:text-navy-200">
                                Proses dilanjutkan dari tahap tersebut
                            </p>
                        </div>

                    @else
                        <div class="flex items-center text-left max-w-xs w-full sm:w-auto">
                            <i class="fa-solid fa-ban text-error mr-2 text-xs shrink-0"></i>
                            <p class="text-xs text-slate-600 dark:text-navy-200 font-bold">
                                Surat ditolak secara permanen
                            </p>
                        </div>
                        <div class="flex items-center text-left max-w-xs w-full sm:w-auto">
                            <i class="fa-solid fa-file-circle-plus text-slate-500 mr-2 text-xs shrink-0"></i>
                            <p class="text-xs text-slate-600 dark:text-navy-200">
                                Mahasiswa harus membuat pengajuan baru
                            </p>
                        </div>
                        <div class="flex items-center text-left max-w-xs w-full sm:w-auto">
                            <i class="fa-solid fa-stop text-error mr-2 text-xs shrink-0"></i>
                            <p class="text-xs text-slate-600 dark:text-navy-200">
                                Proses approval dihentikan total
                            </p>
                        </div>
                    @endif
                </div>
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
