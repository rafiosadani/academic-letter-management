<x-layouts.app :title="isset($studyProgram) ? 'Edit Program Studi: ' . $studyProgram->name : 'Tambah Program Studi Baru'">
    <x-ui.breadcrumb
            :title="isset($studyProgram) ? 'Edit Program Studi' : 'Tambah Program Studi'"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Program Studi', 'url' => route('master.study-programs.index')],
            ['label' => isset($studyProgram) ? 'Edit' : 'Tambah']
        ]"
    />

    <x-ui.page-header
            :title="isset($studyProgram) ? 'Edit Program Studi: ' . $studyProgram->name : 'Tambah Program Studi Baru'"
            :description="isset($studyProgram) ? 'Perbarui informasi program studi' : 'Buat program studi baru di fakultas'"
            :backUrl="route('master.study-programs.index')"
    >
        <x-slot:icon>
            @if(isset($studyProgram))
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            @endif
        </x-slot:icon>
    </x-ui.page-header>

    <form
            method="POST"
            action="{{ isset($studyProgram) ? route('master.study-programs.update', $studyProgram) : route('master.study-programs.store') }}"
            class="space-y-5 grow flex flex-col" {{-- Kunci: 'grow flex flex-col' di form wrapper --}}
    >
        @csrf
        @if(isset($studyProgram))
            @method('PUT')
        @endif

        {{-- Form Content Wrapper --}}
        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6 grow"> {{-- Kunci: Tambahkan 'grow' di sini --}}
            {{-- Main Form --}}
            <div class="col-span-12 lg:col-span-8">
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Informasi Program Studi
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        {{-- Name --}}
                        <x-form.input
                                label="Nama Program Studi"
                                name="name"
                                :value="$studyProgram->name ?? ''"
                                placeholder="Contoh: Teknik Informatika"
                                required
                                helper="Nama lengkap program studi"
                        />

                        <x-form.select
                                label="Jenjang"
                                name="degree"
                                :options="\App\Enums\DegreeEnum::toSelectArray()"
                                :value="isset($studyProgram) ? $studyProgram->degree?->value : ''"
                                placeholder="Pilih jenjang"
                                required
                                helper="Jenjang pendidikan program studi"
                        />

                        @if(isset($studyProgram) && $studyProgram->degree)
                            <div class="rounded-lg bg-info/10 border border-info/20 p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fa-solid fa-info-circle text-info mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-xs text-slate-600 dark:text-navy-200 mb-2">Preview Nama Program Studi:</p>

                                        <div class="space-y-1.5">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-slate-500 dark:text-navy-300 w-24">Format Pendek:</span>
                                                <span class="text-xs font-medium text-slate-700 dark:text-navy-100">{{ $studyProgram->degree_name }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs text-slate-500 dark:text-navy-300 w-24">Format Lengkap:</span>
                                                <span class="text-xs font-medium text-slate-700 dark:text-navy-100">{{ $studyProgram->full_degree_name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Side Info --}}
            <div class="col-span-12 lg:col-span-4 flex flex-col"> {{-- Tambahkan 'flex flex-col' untuk mengatur item samping --}}
                {{-- Help Card --}}
                <div class="card p-4 sm:p-5 space-y-4">
                    <div>
                        <h3 class="text-base font-medium text-slate-700 dark:text-navy-100 flex items-center space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning"></i>
                            <span>Panduan Pengisian</span>
                        </h3>
                    </div>

                    <div class="space-y-3 text-xs text-slate-600 dark:text-navy-200">
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-circle text-[6px] mt-1.5 text-slate-400"></i>
                            <p>Nama program studi harus unik dan tidak boleh duplikat</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-circle text-[6px] mt-1.5 text-slate-400"></i>
                            <p>Pilih jenjang yang sesuai (D3, D4, S1, S2, S3)</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-circle text-[6px] mt-1.5 text-slate-400"></i>
                            <p>Kode program studi akan digenerate otomatis oleh sistem</p>
                        </div>
                    </div>
                </div>

                {{-- Timestamps (Only on Edit) --}}
                @if(isset($studyProgram))
                    <div class="card p-4 sm:p-5 space-y-4 mt-4">
                        <div>
                            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                Informasi Tambahan
                            </h3>
                        </div>

                        <div class="space-y-3">
                            {{-- Code --}}
                            <div>
                                <span class="text-xs text-slate-500 dark:text-navy-300">Kode</span>
                                <p class="font-medium text-slate-700 dark:text-navy-100">{{ $studyProgram->code }}</p>
                            </div>

                            {{-- Timestamps --}}
                            <div class="pt-3 border-t border-slate-200 dark:border-navy-500 space-y-2 text-xs text-slate-500 dark:text-navy-300">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>Dibuat: {{ $studyProgram->created_at_formatted }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Update: {{ $studyProgram->updated_at_formatted }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons (Sticky Bottom) --}}
        <x-form.sticky-form-actions
            :cancelUrl="route('master.study-programs.index')"
            :submitText="isset($studyProgram) ? 'Update Program Studi' : 'Simpan Program Studi'"
            :submitType="isset($studyProgram) ? 'warning' : 'primary'"
        />
    </form>
</x-layouts.app>