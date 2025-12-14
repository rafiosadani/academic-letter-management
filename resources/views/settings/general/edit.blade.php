<x-layouts.app title="Pengaturan Umum">
    <x-ui.breadcrumb
            title="Pengaturan Umum"
            :items="[
            ['label' => 'Pengaturan'],
            ['label' => 'Pengaturan Umum']
        ]"
    />

    <x-ui.page-header
        title="Pengaturan Umum"
        description="Kelola pengaturan aplikasi dan template surat"
    >
        <x-slot:icon>
            <i class="fa-solid fa-cog text-xl"></i>
        </x-slot:icon>
    </x-ui.page-header>

    {{-- FORM --}}
    <form
        method="POST"
        action="{{ route('settings.general.update') }}"
        enctype="multipart/form-data"
        class="space-y-5"
    >
        @csrf
        @method('PUT')

        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">

            {{-- Main Content (Full Width) --}}
            <div class="col-span-12 space-y-5">

                {{-- General Settings Section --}}
                @if(isset($settings['general']))
                    <div class="card">
                        <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                    <i class="fa-solid fa-cog"></i>
                                </div>
                                <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                    Pengaturan Aplikasi
                                </h4>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5 space-y-4">
                            @foreach($settings['general'] as $setting)
                                @if($setting->type === 'image')
                                    <div>
                                        <label class="block">
                                            <span class="text-xs+ font-medium text-slate-600 dark:text-navy-100">
                                                {{ $setting->label }}
                                            </span>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $setting->description }}
                                                </p>
                                            @endif
                                        </label>

                                        {{--Current Image Preview--}}
                                        @if($setting->value)
                                            <div class="mt-2">
                                                <img src="{{ $setting?->getImageUrl() }}"
                                                     alt="{{ $setting->label }}"
                                                     class="h-16 w-auto rounded border border-slate-200 dark:border-navy-500">
                                            </div>
                                        @endif

                                        {{-- Image Upload with Current Preview --}}
                                        <x-form.file
                                            :name="$setting->key"
                                            accept="image/*"
                                            :showPreview="true"
                                            :currentUrl="$setting->value ? $setting?->getImageUrl() : null"
                                            :currentFilename="$setting->value ? basename($setting->value) : null"
                                            buttonText="Pilih Gambar"
                                            changeText="Ganti Gambar"
                                            class="text-xs"
                                        />
                                    </div>
                                @else
                                    <div>
                                        <label class="block">
                                            <span class="text-xs-plus font-medium text-slate-600 dark:text-navy-100">
                                                {{ $setting->label }}
                                            </span>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $setting->description }}
                                                </p>
                                            @endif
                                            <x-form.input
                                                :name="$setting->key"
                                                :value="old($setting->key, $setting->value)"
                                                class="text-xs"
                                            />
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Header Settings Section --}}
                @if(isset($settings['header']))
                    <div class="card">
                        <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success">
                                    <i class="fa-solid fa-heading"></i>
                                </div>
                                <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                    Header Surat
                                </h4>
                            </div>
                            <p class="mt-2 text-xs text-slate-500 dark:text-navy-300">
                                Pengaturan untuk header surat PDF/DOCX
                            </p>
                        </div>

                        <div class="p-4 sm:p-5">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                @foreach($settings['header'] as $setting)
                                    @if($setting->type === 'image')
                                        {{-- Image Upload - Full Width --}}
                                        <div class="sm:col-span-2">
                                            <label class="block">
                                            <span class="text-xs+ font-medium text-slate-600 dark:text-navy-100">
                                                {{ $setting->label }}
                                            </span>
                                                @if($setting->description)
                                                    <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                                                        {{ $setting->description }}
                                                    </p>
                                                @endif
                                            </label>

                                            {{--Current Image Preview--}}
                                            @if($setting->value)
                                                <div class="mt-2">
                                                    <img src="{{ $setting?->getImageUrl() }}"
                                                         alt="{{ $setting->label }}"
                                                         class="h-16 w-auto rounded border border-slate-200 dark:border-navy-500">
                                                </div>
                                            @endif

                                            {{-- Image Upload with Current Preview --}}
                                            <x-form.file
                                                :name="$setting->key"
                                                accept="image/*"
                                                :showPreview="true"
                                                :currentUrl="$setting->value ? $setting?->getImageUrl() : null"
                                                :currentFilename="$setting->value ? basename($setting->value) : null"
                                                buttonText="Pilih Gambar"
                                                changeText="Ganti Gambar"
                                                class="text-xs"
                                            />
                                        </div>
                                    @elseif($setting->type === 'text')
                                        {{-- Textarea - Full Width --}}
                                        <div class="sm:col-span-2">
                                            <label>
                                                <span class="text-xs-plus font-medium text-slate-600 dark:text-navy-100">
                                                    {{ $setting->label }}
                                                </span>
                                                @if($setting->description)
                                                    <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                                                        {{ $setting->description }}
                                                    </p>
                                                @endif
                                                <x-form.textarea
                                                    :name="$setting->key"
                                                    :value="old($setting->key, $setting->value)"
                                                    rows="1"
                                                    class="text-xs"
                                                />
                                            </label>
                                        </div>
                                    @else
                                        {{-- Text Input - Half Width on Desktop --}}
                                        <label>
                                            <span class="text-xs-plus font-medium text-slate-600 dark:text-navy-100">
                                                {{ $setting->label }}
                                            </span>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $setting->description }}
                                                </p>
                                            @endif
                                            <x-form.input
                                                :name="$setting->key"
                                                :value="old($setting->key, $setting->value)"
                                                class="text-xs"
                                            />
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Footer Settings Section --}}
                @if(isset($settings['footer']))
                    <div class="card">
                        <div class="border-b border-slate-200 p-4 dark:border-navy-500 sm:px-5">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                                    <i class="fa-solid fa-align-left"></i>
                                </div>
                                <h4 class="text-lg font-medium text-slate-700 dark:text-navy-100">
                                    Footer Surat
                                </h4>
                            </div>
                            <p class="mt-2 text-xs text-slate-500 dark:text-navy-300">
                                Pengaturan untuk footer surat PDF/DOCX
                            </p>
                        </div>

                        <div class="p-4 sm:p-5 space-y-4">
                            @foreach($settings['footer'] as $setting)
                                @if($setting->type === 'image')
                                    {{-- Image Upload --}}
                                    <div>
                                        <label class="block">
                                            <span class="text-xs+ font-medium text-slate-600 dark:text-navy-100">
                                                {{ $setting->label }}
                                            </span>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $setting->description }}
                                                </p>
                                            @endif
                                        </label>

                                        {{--Current Image Preview--}}
                                        @if($setting->value)
                                            <div class="mt-2">
                                                <img src="{{ $setting?->getImageUrl() }}"
                                                     alt="{{ $setting->label }}"
                                                     class="h-16 w-auto rounded border border-slate-200 dark:border-navy-500">
                                            </div>
                                        @endif

                                        {{-- Image Upload with Current Preview --}}
                                        <x-form.file
                                            :name="$setting->key"
                                            accept="image/*"
                                            :showPreview="true"
                                            :currentUrl="$setting->value ? $setting?->getImageUrl() : null"
                                            :currentFilename="$setting->value ? basename($setting->value) : null"
                                            buttonText="Pilih Gambar"
                                            changeText="Ganti Gambar"
                                            class="text-xs"
                                        />
                                    </div>
                                @else
                                    {{-- Textarea --}}
                                    <div>
                                        <label class="block">
                                            <span class="text-xs+ font-medium text-slate-600 dark:text-navy-100">
                                                {{ $setting->label }}
                                            </span>
                                            @if($setting->description)
                                                <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                                                    {{ $setting->description }}
                                                </p>
                                            @endif
                                            <x-form.textarea
                                                :name="$setting->key"
                                                :value="old($setting->key, $setting->value)"
                                                rows="3"
                                                class="text-xs"
                                            />
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end space-x-2">
            <button
                type="submit"
                class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90"
            >
                <i class="fa-solid fa-save mr-2"></i>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</x-layouts.app>