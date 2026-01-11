{{-- Upload Final PDF Modal --}}
<div class="modal fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
     id="upload-pdf-modal-{{ $letter->id }}"
     data-open-on-error="{{ session('open_upload_final_pdf_modal_id') == $letter->id ? 'true' : 'false' }}"
     role="dialog"
>
    <div class="modal-overlay absolute inset-0 bg-slate-900/60 transition-opacity duration-300"></div>
        <div class="modal-content relative flex w-full max-w-xl origin-top flex-col overflow-y-auto rounded-lg bg-white dark:bg-navy-700 transition-all duration-300">
            <div class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-4 dark:bg-navy-800 sm:px-5">
                <div class="flex items-center space-x-2">
                    {{-- Ikon Box: Menggunakan warna Success (Hijau) --}}
                    <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 text-success dark:bg-success/15">
                        <i class="fa-solid fa-cloud-arrow-up text-sm"></i>
                    </div>
                    <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                        Upload PDF Final
                    </h4>
                </div>
                <button
                    data-close-modal
                    class="btn size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25"
                >
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <div class="scrollbar-sm overflow-y-auto px-4 py-4 sm:px-5">
                {{-- Info --}}
                <div class="rounded-lg bg-info/10 border border-info/20 p-3 mb-4 text-left">
                    <p class="text-xs text-slate-600 dark:text-navy-200">
                        <i class="fa-solid fa-info-circle text-info mr-1"></i>
                        Upload PDF final yang sudah ditandatangani dari proses eksternal (UB Pusat).
                    </p>
                </div>

                {{-- Form --}}
                <form method="POST"
                      action="{{ route('letters.upload-pdf', $letter) }}"
                      enctype="multipart/form-data"
                      class="text-left">
                    @csrf

                    <div class="space-y-4">
                        {{-- File Upload --}}
                        <div>
                            <div class="mb-3">
                                <span class="font-medium text-slate-600 dark:text-navy-100">
                                    File PDF Final <span class="text-error">*</span>
                                </span>
                                <p class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1">
                                    Lampirkan file PDF yang telah ditandatangani (Maks. 5MB)
                                </p>
                            </div>

                            <div x-data="{ fileName: 'Belum ada file dipilih' }"
                                 class="flex items-stretch min-h-[40px] rounded-lg border border-slate-300 dark:border-navy-450 overflow-hidden hover:border-slate-400 dark:hover:border-navy-400 transition-colors bg-white dark:bg-navy-700"
                            >
                                <label class="flex items-center gap-2 px-4 min-w-[100px] cursor-pointer bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-navy-600 dark:text-navy-100 dark:hover:bg-navy-500 transition-colors">
                                    <i class="fa-solid fa-file-pdf text-sm text-error"></i>
                                    <span class="text-xs font-medium">Pilih File</span>

                                    <input
                                            type="file"
                                            name="final_pdf" {{-- Nama input disesuaikan dengan Controller --}}
                                            class="pointer-events-none absolute inset-0 h-full w-full opacity-0"
                                            accept=".pdf"
                                            @change="fileName = $event.target.files[0]?.name ?? 'Belum ada file dipilih'"
                                    >
                                </label>

                                {{-- Divider --}}
                                <div class="w-px bg-slate-300 dark:bg-navy-400"></div>

                                {{-- File Name Display --}}
                                <div class="flex flex-1 items-center min-w-0 px-3 py-2">
                                    <span x-text="fileName"
                                        :title="fileName"
                                        class="block w-full truncate text-tiny-plus text-slate-600 dark:text-navy-200 font-mono">
                                    </span>
                                </div>
                            </div>

                            @error('final_pdf')
                            <span class="text-tiny-plus text-error mt-1.5 ms-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <x-form.input
                                    label="Nomor Surat"
                                    name="letter_number"
                                    type="text"
                                    {{--:value=""--}}
                                    placeholder="Contoh: 00785/UN10.F1601/B/PP/2025"
                                    helper="Nomor surat dari UB Pusat"
                                    required
                            />
                        </div>

                        <div>
                            <x-form.textarea
                                label="Catatan (Opsional)"
                                name="note"
                                {{--:value=""--}}
                                placeholder="Tambahkan catatan jika diperlukan..."
                                rows="2"
                            />
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-6 flex space-x-2">
                        <button
                            type="button"
                            data-close-modal
                            class="btn flex-1 border border-slate-300 font-medium text-slate-800 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-50 dark:hover:bg-navy-500"
                        >
                            <i class="fa-solid fa-xmark mr-2"></i>
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="btn flex-1 bg-success font-medium text-white hover:bg-success-focus focus:bg-success-focus active:bg-success-focus/90"
                        >
                            <i class="fa-solid fa-upload mr-2"></i>
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>