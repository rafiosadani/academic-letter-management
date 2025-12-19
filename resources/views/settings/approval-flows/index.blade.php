<x-layouts.app title="Alur Persetujuan Surat">
    <x-ui.breadcrumb
            title="Alur Persetujuan"
            :items="[
            ['label' => 'Pengaturan'],
            ['label' => 'Alur Persetujuan Surat']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <x-ui.table-header
                title="Alur Persetujuan Surat"
                description="Kelola alur persetujuan untuk setiap jenis surat"
                :showSearch="false"
            />

            {{-- Filters --}}
            <div class="card mt-3 p-4">
                <form method="GET" action="{{ route('settings.approval-flows.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    {{-- Letter Type Filter --}}
                    <div>
                        <label class="block">
                            <span class="text-xs-plus font-medium text-slate-600 dark:text-navy-100">Jenis Surat</span>
                            <select name="letter_type"
                                    class="form-select mt-1 h-8 w-full rounded-lg border border-slate-300 bg-white px-2.5 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent text-xs">
                                <option value="">Semua Jenis Surat</option>
                                @foreach($letterTypes as $type)
                                    <option value="{{ $type->value }}" {{ $letterTypeFilter == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                                class="btn h-8 bg-primary text-xs-plus text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                            <i class="fa-solid fa-filter mr-2"></i>
                            Filter
                        </button>
                        <a href="{{ route('settings.approval-flows.index') }}"
                           class="btn h-8 bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Info Card --}}
            @if(!empty($groupedFlows))
                <div class="card mt-3 p-4 bg-info/5 border border-info/20">
                    <div class="flex items-start space-x-3">
                        <i class="fa-solid fa-circle-info text-info text-lg mt-0.5"></i>
                        <div class="flex-1">
                            <h3 class="text-xs-plus font-medium text-slate-700 dark:text-navy-100">
                                Cara Mengelola Alur Persetujuan
                            </h3>
                            <ul class="mt-2 space-y-1 text-xs text-slate-600 dark:text-navy-200">
                                <li class="flex items-center space-x-2">
                                    <i class="fa-solid fa-check text-success mt-0.5 text-tiny"></i>
                                    <span>Klik <strong>"Detail"</strong> untuk melihat timeline lengkap alur persetujuan</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <i class="fa-solid fa-check text-success mt-0.5 text-tiny"></i>
                                    <span>Di halaman detail, gunakan <strong>"Tambah Step"</strong> untuk menambah step baru</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <i class="fa-solid fa-check text-success mt-0.5 text-tiny"></i>
                                    <span>Edit atau hapus step individual langsung dari timeline</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <i class="fa-solid fa-check text-success mt-0.5 text-tiny"></i>
                                    <span>Status <strong>"Complete"</strong> berarti alur sudah memiliki step final</span>
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
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                Total Step
                            </th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">
                                Status
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
                        @forelse($groupedFlows as $group)
                            @php
                                $firstFlow = $group['first_flow'];
                            @endphp
                            <tr class="text-xs border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                {{-- Letter Type --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex size-11 items-center justify-center rounded-lg bg-{{ $group['letter_type']->color() }}/10 text-{{ $group['letter_type']->color() }}">
                                            <i class="fa-solid {{ $group['letter_type']->icon() }} text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-slate-700 dark:text-navy-100">
                                                {{ $group['letter_type']->label() }}
                                            </p>
                                            <p class="text-tiny-plus text-slate-400 dark:text-navy-300">
                                                {{ $group['letter_type']->description() }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Total Steps --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <div class="inline-flex items-center space-x-1 rounded-full bg-slate-100 dark:bg-navy-600 px-2 py-0.5 border border-slate-200 dark:border-navy-500">
                                        <i class="fa-solid fa-list-ol text-xs text-slate-500 dark:text-navy-200"></i>
                                        <span class="font-medium text-slate-700 dark:text-navy-100">{{ $group['total_steps'] }}</span>
                                        <span class="text-tiny text-slate-500 dark:text-navy-200">step{{ $group['total_steps'] > 1 ? 's' : '' }}</span>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    @if($group['is_complete'])
                                        <span class="badge bg-success/10 text-success text-tiny">
                                            <i class="fa-solid fa-check-circle mr-1"></i>
                                            Complete
                                        </span>
                                    @else
                                        <span class="badge bg-warning/10 text-warning text-tiny">
                                            <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                            Incomplete
                                        </span>
                                    @endif
                                </td>

                                {{-- Creator/Deleter --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-xs leading-4">
                                    @if($firstFlow)
                                        <div class="flex flex-col">
                                            <span class="text-slate-700 dark:text-navy-100">
                                                {{ $firstFlow?->created_by_name }}
                                            </span>
                                            <span class="text-slate-400">
                                                {{ $firstFlow->created_at_formatted }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    @if($firstFlow)
                                        <div class="flex items-center justify-center space-x-2">
                                            {{-- View Detail Button --}}
                                            @can('view', $firstFlow)
                                                <a href="{{ route('settings.approval-flows.show', $firstFlow) }}"
                                                   class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25"
                                                   title="Detail">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-tiny">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td colspan="5" class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <span class="text-xs-plus">Data tidak ditemukan.</span>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>