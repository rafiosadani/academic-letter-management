<x-layouts.app title="Pengaturan Umum">
    <x-ui.breadcrumb
            title="Pengaturan Umum"
            :items="[
            ['label' => 'Pengaturan'],
            ['label' => 'Pengaturan Umum']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            {{-- Page Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-medium tracking-wide text-slate-700 dark:text-navy-100 lg:text-lg">
                        Pengaturan Umum
                    </h2>
                    <p class="mt-1 text-xs+ text-slate-400 dark:text-navy-300">
                        Kelola pengaturan aplikasi dan template surat
                    </p>
                </div>
            </div>

             Form
            <form
                    method="POST"
                    action="{{ route('settings.general.update') }}"
                    enctype="multipart/form-data"
                    class="mt-4 space-y-5"
            >
                @csrf
                @method('PUT')

                {{-- General Settings Section --}}
                @if(isset($settings['general']))
                    <div class="card p-4 sm:p-5">
                        <div class="border-b border-slate-200 pb-4 dark:border-navy-500">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-primary/10 p-1 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                                    <i class="fa-solid fa-cog"></i>
                                </div>
                                <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                    Pengaturan Aplikasi
                                </h4>
                            </div>
                        </div>

                        <div class="mt-4 space-y-4">
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

                                         Current Image Preview
                                        @if($setting->value)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $setting->value) }}"
                                                     alt="{{ $setting->label }}"
                                                     class="h-16 w-auto rounded border border-slate-200 dark:border-navy-500">
                                            </div>
                                        @endif

                                        <input
                                                type="file"
                                                name="{{ $setting->key }}"
                                                accept="image/*"
                                                class="form-input mt-2 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 text-xs placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        />
                                        @error($setting->key)
                                        <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
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
                                            <input
                                                    type="text"
                                                    name="{{ $setting->key }}"
                                                    value="{{ old($setting->key, $setting->value) }}"
                                                    class="form-input mt-2 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 text-xs placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            />
                                        </label>
                                        @error($setting->key)
                                        <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Header Settings Section --}}
                @if(isset($settings['header']))
                    <div class="card p-4 sm:p-5">
                        <div class="border-b border-slate-200 pb-4 dark:border-navy-500">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-success/10 p-1 text-success">
                                    <i class="fa-solid fa-heading"></i>
                                </div>
                                <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                    Header Surat
                                </h4>
                            </div>
                            <p class="mt-2 text-xs text-slate-500 dark:text-navy-300">
                                Pengaturan untuk header surat PDF/DOCX
                            </p>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @foreach($settings['header'] as $setting)
                                @if($setting->type === 'image')
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

                                        @if($setting->value)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $setting->value) }}"
                                                     alt="{{ $setting->label }}"
                                                     class="h-16 w-auto rounded border border-slate-200 dark:border-navy-500">
                                            </div>
                                        @endif

                                        <input
                                                type="file"
                                                name="{{ $setting->key }}"
                                                accept="image/*"
                                                class="form-input mt-2 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 text-xs placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        />
                                        @error($setting->key)
                                        <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @elseif($setting->type === 'text')
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
                                            <textarea
                                                    name="{{ $setting->key }}"
                                                    rows="2"
                                                    class="form-input mt-2 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 text-xs placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            >{{ old($setting->key, $setting->value) }}</textarea>
                                        </label>
                                        @error($setting->key)
                                        <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
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
                                            <input
                                                    type="text"
                                                    name="{{ $setting->key }}"
                                                    value="{{ old($setting->key, $setting->value) }}"
                                                    class="form-input mt-2 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 text-xs placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            />
                                        </label>
                                        @error($setting->key)
                                        <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Footer Settings Section --}}
                @if(isset($settings['footer']))
                    <div class="card p-4 sm:p-5">
                        <div class="border-b border-slate-200 pb-4 dark:border-navy-500">
                            <div class="flex items-center space-x-2">
                                <div class="flex size-7 items-center justify-center rounded-lg bg-info/10 p-1 text-info">
                                    <i class="fa-solid fa-align-left"></i>
                                </div>
                                <h4 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                    Footer Surat
                                </h4>
                            </div>
                            <p class="mt-2 text-xs text-slate-500 dark:text-navy-300">
                                Pengaturan untuk footer surat PDF/DOCX
                            </p>
                        </div>

                        <div class="mt-4 space-y-4">
                            @foreach($settings['footer'] as $setting)
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

                                        @if($setting->value)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $setting->value) }}"
                                                     alt="{{ $setting->label }}"
                                                     class="h-16 w-auto rounded border border-slate-200 dark:border-navy-500">
                                            </div>
                                        @endif

                                        <input
                                                type="file"
                                                name="{{ $setting->key }}"
                                                accept="image/*"
                                                class="form-input mt-2 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 text-xs placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        />
                                        @error($setting->key)
                                        <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
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
                                            <textarea
                                                    name="{{ $setting->key }}"
                                                    rows="3"
                                                    class="form-input mt-2 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 text-xs placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            >{{ old($setting->key, $setting->value) }}</textarea>
                                        </label>
                                        @error($setting->key)
                                        <span class="text-xs text-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

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

{{--                <div class="sticky bottom-0 z-10 bg-slate-50 dark:bg-navy-800 border-t border-slate-200 dark:border-navy-600 py-4 -mx-[var(--margin-x)] px-[var(--margin-x)] mt-auto -mb-10">--}}
{{--                    <div class="flex items-center justify-end space-x-3">--}}
{{--                        --}}{{-- Submit Button --}}
{{--                        <button--}}
{{--                            type="submit"--}}
{{--                            class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90"--}}
{{--                        >--}}
{{--                            <i class="fa-solid fa-save mr-2"></i>--}}
{{--                            <i class="fa-solid fa-check mr-2"></i>--}}
{{--                            Simpan Pengaturan--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </form>
        </div>
    </div>
</x-layouts.app>