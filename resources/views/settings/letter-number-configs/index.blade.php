<x-layouts.app title="Konfigurasi Nomor Surat">
    <x-ui.breadcrumb
            title="Konfigurasi Nomor Surat"
            :items="[
            ['label' => 'Pengaturan'],
            ['label' => 'Konfigurasi Nomor Surat']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <x-ui.table-header
                title="Konfigurasi Nomor Surat"
                description="Kelola format penomoran surat untuk setiap jenis surat"
                :showSearch="false"

                :policyModel="App\Models\LetterNumberConfig::class"

                :createRoute="$canCreate ? route('settings.letter-number-configs.create') : null"
                createText="Tambah Konfigurasi"
            />

            {{-- Info Card --}}
            @if($configs->isNotEmpty())
                <div class="card mt-3 p-4 bg-info/5 border border-info/20">
                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-circle-info text-info text-lg mt-0.5"></i>
                        <div class="flex-1">
                            <h3 class="text-xs-plus font-medium text-slate-700 dark:text-navy-100">
                                Informasi Penting
                            </h3>
                            <ul class="mt-2 space-y-1 text-xs text-slate-600 dark:text-navy-200">
                                <li class="flex items-center space-x-2">
                                    <i class="fa-solid fa-check text-success mt-0.5 text-tiny"></i>
                                    <span>Counter akan <strong>reset otomatis</strong> setiap tahun baru</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <i class="fa-solid fa-check text-success mt-0.5 text-tiny"></i>
                                    <span>Nomor surat di-generate otomatis saat <strong>step final approved</strong></span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <i class="fa-solid fa-check text-success mt-0.5 text-tiny"></i>
                                    <span>Format Word (SKAK) tidak perlu konfigurasi (sistem eksternal)</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Table --}}
            <div class="card mt-3 p-4">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto border-1 border-slate-200 dark:border-navy-500 rounded-lg">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                        <tr class="text-xs">
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                Jenis Surat
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                Format
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                Counter {{ now()->year }}
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                Dibuat Oleh
                            </th>
                            <th class="whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($configs as $config)
                            <tr class="text-xs border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                {{-- Letter Type --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex size-11 items-center justify-center rounded-lg bg-{{ $config->letter_type->color() }}/10 text-{{ $config->letter_type->color() }}">
                                            <i class="fa-solid {{ $config->letter_type->icon() }} text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-700 dark:text-navy-100">
                                                {{ $config->letter_type->label() }}
                                            </p>
                                            <p class="text-tiny-plus text-slate-400 dark:text-navy-300">
                                                {{ $config->letter_type->shortLabel() }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Format --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="space-y-1">
                                        <code class="inline-flex items-center space-x-1 rounded bg-slate-150 px-2 py-1 text-tiny-plus font-mono text-slate-800 dark:bg-navy-500 dark:text-navy-100">
                                            <span>{seq}</span>
                                            <span class="text-slate-400">/</span>
                                            <span>{{ $config->prefix }}</span>
                                            <span class="text-slate-400">/</span>
                                            <span class="text-primary">{{ $config->code }}</span>
                                            <span class="text-slate-400">/</span>
                                            <span>{year}</span>
                                        </code>
                                        <p class="text-xs text-slate-500 dark:text-navy-300">
                                            Preview: {{ $config->generatePreview($sequences[$config->letter_type->value] + 1) }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Counter --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <div class="inline-flex items-center space-x-1 rounded-full bg-slate-100 dark:bg-navy-600 px-2 py-0.5 border border-slate-200 dark:border-navy-500">
                                        <i class="fa-solid fa-list-ol text-xs text-slate-500 dark:text-navy-200"></i>
                                        <span class="font-medium text-tiny-plus text-slate-700 dark:text-navy-100">
                                            {{ str_pad($sequences[$config->letter_type->value], $config->padding, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Creator --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-xs leading-4">
                                    <div class="flex flex-col">
                                            <span class="text-slate-700 dark:text-navy-100">
                                                {{ $config?->created_by_name }}
                                            </span>
                                        <span class="text-slate-400">
                                                {{ $config?->created_at_formatted }}
                                            </span>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        {{-- Edit --}}
                                        @can('update', $config)
                                            <a href="{{ route('settings.letter-number-configs.edit', $config) }}"
                                               class="btn size-8 p-0 text-warning hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
                                               title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                        @endcan

                                        {{-- Delete --}}
                                        @can('delete', $config)
                                            <button type="button"
                                                    data-toggle="modal"
                                                    data-target="#delete-config-modal-{{ $config->id }}"
                                                    class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                    title="Hapus">
                                                <i class="fa-solid fa-trash-alt"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td colspan="5" class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <div class="py-8">
                                        <i class="fa-solid fa-inbox text-4xl text-slate-300 dark:text-navy-400"></i>
                                        <p class="mt-2 text-xs-plus text-slate-500 dark:text-navy-300">
                                            Belum ada konfigurasi nomor surat.
                                        </p>
                                        @can('create', App\Models\LetterNumberConfig::class)
                                            <a href="{{ route('settings.letter-number-configs.create') }}"
                                               class="btn mt-3 h-8 bg-primary text-xs text-white hover:bg-primary-focus">
                                                <i class="fa-solid fa-plus mr-1"></i>
                                                Tambah Konfigurasi
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @foreach($configs as $config)
        @include('settings.letter-number-configs.modals._delete', ['config' => $config])
    @endforeach
</x-layouts.app>