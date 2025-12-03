<x-layouts.app title="Master Data Tahun Akademik">
    <x-ui.breadcrumb
            title="Data Tahun Akademik"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Tahun Akademik']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <x-ui.table-header
                    title="Data Tahun Akademik"
                    description="Kelola tahun akademik dan semester"
                    search-placeholder="Cari tahun akademik..."
                    :has-deleted-view="false"
                    :create-route="route('master.academic-years.create')"
                    create-text="Tambah Tahun Akademik"
            />

            <div class="card mt-3 p-4">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto border-1 border-slate-200 dark:border-navy-500 rounded-lg">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                        <tr class="text-xs">
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">No</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Kode Tahun Akademik</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Tahun Akademik</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Periode</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Status</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Dibuat Oleh</th>
                            <th class="whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($academicYears as $academicYear)
                            <tr class="text-xs border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    {{ $loop->iteration + ($academicYears->currentPage() - 1) * $academicYears->perPage() }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span class="font-medium text-slate-700 dark:text-navy-100">{{ $academicYear->code }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span class="font-medium text-slate-700 dark:text-navy-100">{{ $academicYear->year_label }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span class="text-slate-600 dark:text-navy-100">{{ $academicYear->period_text }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-tiny text-center">
                                    {!! $academicYear->status_badge !!}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-xs leading-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col">
                                            <span class="text-slate-700 dark:text-navy-100">
                                                {{ $academicYear->created_by_name }}
                                            </span>
                                            <span class="text-slate-700 dark:text-navy-100">
                                                {{ $academicYear->created_at_formatted }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        {{-- Detail Button --}}
                                        <a href="{{ route('master.academic-years.show', $academicYear) }}"
                                           class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25"
                                           title="Detail">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        {{-- Edit Button --}}
                                        <a href="{{ route('master.academic-years.edit', $academicYear) }}"
                                           class="btn size-8 p-0 text-warning hover:bg-warning/20 focus:bg-warning/20 active:bg-warning/25"
                                           title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        {{-- Delete Button --}}
                                        <button
                                                type="button"
                                                data-toggle="modal"
                                                data-target="#delete-academic-year-modal-{{ $academicYear->id }}"
                                                class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                                title="Hapus"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-y border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <td colspan="6"
                                    class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    <span class="text-xs-plus">Data tidak ditemukan.</span>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($academicYears->hasPages())
                    <div class="flex justify-center pt-3">
                        {{ $academicYears->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Delete (Permanent) --}}
    @foreach($academicYears as $academicYear)
        @include('master.academic-years.modals._delete', ['academicYear' => $academicYear])
    @endforeach
</x-layouts.app>