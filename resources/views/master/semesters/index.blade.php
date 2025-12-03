<x-layouts.app title="Master Data Semester">
    <x-ui.breadcrumb
            title="Data Semester"
            :items="[
            ['label' => 'Master Data'],
            ['label' => 'Semester']
        ]"
    />

    <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
        <div class="col-span-12">
            <x-ui.table-header
                    title="Kelola Semester Aktif"
                    description="Aktifkan semester yang sedang berjalan"
                    search-placeholder="Cari semester..."
                    :has-deleted-view="false"
            />

            <div class="card mt-3 p-4">
                <div class="is-scrollbar-hidden min-w-full overflow-x-auto border-1 border-slate-200 dark:border-navy-500 rounded-lg">
                    <table class="is-hoverable w-full text-left">
                        <thead>
                        <tr class="text-xs">
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">No</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Kode Semester</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Tahun Akademik</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Semester</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Periode</th>
                            <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Status</th>
                            <th class="whitespace-nowrap rounded-tr-lg bg-slate-200 px-4 py-3 font-medium text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5 text-center">Action Aktif</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($semesters as $semester)
                            <tr class="text-xs border-y border-transparent border-b-slate-200 dark:border-b-navy-500 {{ $semester->is_active ? 'bg-success/5' : '' }}">
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    {{ $loop->iteration + ($semesters->currentPage() - 1) * $semesters->perPage() }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span class="font-medium text-slate-700 dark:text-navy-100">{{ $semester->code }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-700 dark:text-navy-100">{{ $semester->academicYear->year_label }}</span>
                                        <span class="text-xs text-slate-500 dark:text-navy-300">{{ $semester->academicYear->period_text }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-tiny text-center">
                                    {!! $semester->semester_badge !!}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <span class="text-slate-600 dark:text-navy-100">{{ $semester->period_text }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-tiny text-center">
                                    {!! $semester->status_badge !!}
                                </td>
{{--                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">--}}
{{--                                    @if(!$semester->is_active)--}}
{{--                                        <button--}}
{{--                                                type="button"--}}
{{--                                                data-toggle="modal"--}}
{{--                                                data-target="#toggle-active-semester-modal-{{ $semester->id }}"--}}
{{--                                                class="btn size-8 p-0 text-success hover:bg-success/20 focus:bg-success/20 active:bg-success/25"--}}
{{--                                                title="Aktifkan Semester"--}}
{{--                                        >--}}
{{--                                            <i class="fa-solid fa-check-circle"></i>--}}
{{--                                        </button>--}}
{{--                                    @else--}}
{{--                                        <span class="badge bg-success/10 text-success">--}}
{{--                                            <i class="fa-solid fa-circle-check mr-1"></i>--}}
{{--                                            Semester Aktif--}}
{{--                                        </span>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5 text-center">
                                    @if(!$semester->is_active)
                                        <div class="flex flex-col items-center gap-0.5">
                                            <button
                                                    type="button"
                                                    data-toggle="modal"
                                                    data-target="#toggle-active-semester-modal-{{ $semester->id }}"
                                                    class="btn size-8 p-0 {{ $semester->activation_button_color }}"
                                                    title="{{ $semester->activation_button_title }}"
                                            >
                                                <i class="{{ $semester->activation_button_icon }}"></i>
                                            </button>
                                            <span class="text-[9px] text-slate-500 dark:text-navy-300">{{ $semester->activation_text }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-success/10 text-success">
                                            <i class="fa-solid fa-circle-check mr-1"></i>
                                            Semester Aktif
                                        </span>
                                    @endif
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

                @if ($semesters->hasPages())
                    <div class="flex justify-center pt-3">
                        {{ $semesters->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Toggle Active --}}
    @foreach($semesters as $semester)
        @if(!$semester->is_active)
            @include('master.semesters.modals._toggle-active', ['semester' => $semester])
        @endif
    @endforeach
</x-layouts.app>