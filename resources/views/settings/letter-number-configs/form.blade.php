<x-layouts.app :title="isset($letterNumberConfig) ? 'Edit Konfigurasi Nomor Surat' : 'Tambah Konfigurasi Nomor Surat'">
    <x-ui.breadcrumb
            :title="isset($letterNumberConfig) ? 'Edit Konfigurasi' : 'Tambah Konfigurasi'"
            :items="[
            ['label' => 'Pengaturan'],
            ['label' => 'Konfigurasi Nomor Surat', 'url' => route('settings.letter-number-configs.index')],
            ['label' => isset($letterNumberConfig) ? 'Edit' : 'Tambah']
        ]"
    />

    <x-ui.page-header
            :title="isset($letterNumberConfig) ? 'Edit Konfigurasi Nomor Surat' : 'Tambah Konfigurasi Nomor Surat'"
            :description="isset($letterNumberConfig) ? 'Perbarui format penomoran surat' : 'Buat konfigurasi format penomoran baru'"
            :backUrl="route('settings.letter-number-configs.index')"
    >
        <x-slot:icon>
            @if(isset($letterNumberConfig))
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            @endif
        </x-slot:icon>
    </x-ui.page-header>

    {{-- FORM --}}
    <form
            method="POST"
            action="{{ isset($letterNumberConfig) ? route('settings.letter-number-configs.update', $letterNumberConfig) : route('settings.letter-number-configs.store') }}"
            class="space-y-5 grow flex flex-col"
            x-data="{
            prefix: '{{ old('prefix', $letterNumberConfig->prefix ?? 'UN10.F1601') }}',
            code: '{{ old('code', $letterNumberConfig->code ?? '') }}',
            padding: {{ old('padding', $letterNumberConfig->padding ?? 3) }},
            getPreview() {
                if (!this.code) return 'Masukkan kode surat...';
                const seq = '1'.padStart(this.padding, '0');
                const year = new Date().getFullYear();
                return `${seq}/${this.prefix}/${this.code}/${year}`;
            }
        }"
    >
        @csrf
        @if(isset($letterNumberConfig))
            @method('PUT')
        @endif

        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6 grow">
            {{-- Main Form --}}
            <div class="col-span-12 lg:col-span-7 space-y-5">
                {{-- Configuration --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                <i class="fa-solid fa-cog"></i>
                            </div>
                            <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                Format Penomoran
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        {{-- Letter Type --}}
                        <x-form.select
                                label="Jenis Surat"
                                name="letter_type"
                                :options="$letterTypes->mapWithKeys(fn($type) => [$type->value => $type->label()])->toArray()"
                                :value="old('letter_type', $letterNumberConfig->letter_type->value ?? '')"
                                placeholder="Pilih jenis surat"
                                required
                                :readonly="isset($letterNumberConfig)"
                                helper="Hanya jenis surat PDF yang perlu konfigurasi (bukan Word/SKAK)"
                        />

                        {{-- Prefix --}}
                        <x-form.input
                                label="Prefix (Kode Unit)"
                                name="prefix"
                                type="text"
                                :value="old('prefix', $letterNumberConfig->prefix ?? 'UN10.F1601')"
                                placeholder="Contoh: UN10.F1601"
                                required
                                helper="Kode unit fakultas/universitas"
                                x-model="prefix"
                        />

                        {{-- Code --}}
                        <x-form.input
                                label="Kode Surat"
                                name="code"
                                type="text"
                                :value="old('code', $letterNumberConfig->code ?? '')"
                                placeholder="Contoh: LL, DK, DM"
                                required
                                helper="Kode singkat jenis surat (2-3 huruf)"
                                x-model="code"
                        />

                        {{-- Padding --}}
                        <x-form.input
                                label="Padding (Jumlah Digit)"
                                name="padding"
                                type="number"
                                min="1"
                                max="10"
                                :value="old('padding', $letterNumberConfig->padding ?? 3)"
                                required
                                helper="Jumlah digit nomor urut (3 = 001, 4 = 0001)"
                                x-model="padding"
                        />

                        {{-- Preview --}}
                        <div class="rounded-lg bg-slate-100 dark:bg-navy-600 p-4">
                            <label class="block text-xs font-medium text-slate-600 dark:text-navy-100 mb-2">
                                Preview Format:
                            </label>
                            <code class="block text-lg font-mono font-bold text-primary dark:text-accent" x-text="getPreview()"></code>
                            <p class="text-xs text-slate-500 dark:text-navy-300 mt-2">
                                Format: {sequence}/{prefix}/{code}/{year}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Info --}}
            <div class="col-span-12 lg:col-span-5 space-y-5">
                {{-- Info Card --}}
                <div class="card">
                    <div class="border-b border-slate-200 p-4 dark:border-navy-500">
                        <div class="flex items-center space-x-2">
                            <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                Panduan
                            </h4>
                        </div>
                    </div>

                    <div class="p-4 space-y-3 text-xs text-slate-600 dark:text-navy-200">
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                            <p><strong>Prefix:</strong> Kode unit fakultas (contoh: UN10.F1601 untuk Fakultas Vokasi UB)</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                            <p><strong>Kode:</strong> Singkatan jenis surat (LL, DK, DM)</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                            <p><strong>Padding:</strong> Menentukan berapa digit nomor urut (3 = 001, 002, ...)</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                            <p>Nomor akan di-generate otomatis saat approval final</p>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-warning mt-0.5"></i>
                            <p>Counter akan reset otomatis setiap tahun baru</p>
                        </div>
                    </div>

                    @if(isset($letterNumberConfig))
                        <div class="p-4 border-t border-slate-200 dark:border-navy-500">
                            <div class="space-y-2 text-xs text-slate-500 dark:text-navy-300">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>Dibuat: {{ $letterNumberConfig->created_at_formatted }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Update: {{ $letterNumberConfig->updated_at_formatted }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Buttons (Sticky Bottom) --}}
        <x-form.sticky-form-actions
                :cancelUrl="route('settings.letter-number-configs.index')"
                :submitText="isset($letterNumberConfig) ? 'Update Konfigurasi' : 'Simpan Konfigurasi'"
                :submitType="isset($letterNumberConfig) ? 'warning' : 'primary'"
        />
    </form>
</x-layouts.app>