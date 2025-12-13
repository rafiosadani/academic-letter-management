<div class="card p-4">
    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:items-center sm:justify-between w-full">
        {{-- Title & Description --}}
        <div>
            <h2 class="text-sm sm:text-base font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100">
                {{ $isDeletedView ? $title . ' - Deleted Records' : $title }}
            </h2>
            @if($description)
                <p class="text-tiny-plus sm:text-xs+">
                    {{ $isDeletedView
                        ? 'Kelola data yang telah dihapus.'
                        : $description
                    }}
                </p>
            @endif
        </div>

        {{-- Actions: Search & Buttons --}}
        <div class="flex flex-col space-y-3 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-2">

            {{-- Search Form --}}
            @if($showSearch)
                <form method="GET" class="w-full sm:w-80">
                    {{-- Preserve query params --}}
                    @if($isDeletedView)
                        <input type="hidden" name="view_deleted" value="1">
                    @endif
                    @foreach(request()->except(['search', 'view_deleted', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <label class="relative flex">
                        <input
                                name="search"
                                value="{{ $searchValue }}"
                                class="form-input peer h-8 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 text-tiny sm:text-xs placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                placeholder="{{ $searchPlaceholder }}"
                                type="text"
                        />
                        <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 transition-colors duration-200" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3.316 13.781l.73-.171-.73.171zm0-5.457l.73.171-.73-.171zm15.473 0l.73-.171-.73.171zm0 5.457l.73.171-.73-.171zm-5.008 5.008l-.171-.73.171.73zm-5.457 0l-.171.73.171-.73zm0-15.473l-.171-.73.171.73zm5.457 0l.171-.73-.171.73zM20.47 21.53a.75.75 0 101.06-1.06l-1.06 1.06zM4.046 13.61a11.198 11.198 0 010-5.115l-1.46-.342a12.698 12.698 0 000 5.8l1.46-.343zm14.013-5.115a11.196 11.196 0 010 5.115l1.46.342a12.698 12.698 0 000-5.8l-1.46.343zm-4.45 9.564a11.196 11.196 0 01-5.114 0l-.342 1.46c1.907.448 3.892.448 5.8 0l-.343-1.46zM8.496 4.046a11.198 11.198 0 015.115 0l.342-1.46a12.698 12.698 0 00-5.8 0l.343 1.46zm0 14.013a5.97 5.97 0 01-4.45-4.45l-1.46.343a7.47 7.47 0 005.568 5.568l.342-1.46zm5.457 1.46a7.47 7.47 0 005.568-5.567l-1.46-.342a5.97 5.97 0 01-4.45 4.45l.342 1.46zM13.61 4.046a5.97 5.97 0 014.45 4.45l1.46-.343a7.47 7.47 0 00-5.568-5.567l-.342 1.46zm-5.457-1.46a7.47 7.47 0 00-5.567 5.567l1.46.342a5.97 5.97 0 014.45-4.45l-.343-1.46zm8.652 15.28l3.665 3.664 1.06-1.06-3.665-3.665-1.06 1.06z"/>
                            </svg>
                        </span>
                    </label>
                </form>
            @endif

            {{-- Custom Action Buttons Slot --}}
            {{ $slot }}

            {{-- Action Buttons --}}
            @if($isDeletedView)
                @if(!empty($policyModel))
                    {{-- With Policy --}}
                    {{-- Deleted View: Back to All + Restore All --}}
                    @if($indexRoute)
                        @can('viewAny', $policyModel)
                            <a href="{{ $indexRoute }}"
                               class="btn w-full sm:w-auto justify-center bg-slate-500 font-normal text-white hover:bg-slate-600 focus:bg-slate-600 active:bg-slate-600/90">
                                <i class="fa-solid fa-list mr-2 text-tiny sm:text-xs"></i>
                                <span class="text-tiny sm:text-xs">Lihat Semua</span>
                            </a>
                        @endcan
                    @endif

                    @if($deletedCount > 0 && $restoreAllModalId)
                        @can('restore', $policyModel)
                            <button type="button"
                                    data-toggle="modal"
                                    data-target="#{{ $restoreAllModalId }}"
                                    class="btn w-full sm:w-auto justify-center bg-success font-normal text-white hover:bg-success/90 focus:bg-success/90 active:bg-success/80">
                                <i class="fa-solid fa-undo mr-2 text-tiny sm:text-xs"></i>
                                <span class="text-tiny sm:text-xs">Restore All ({{ $deletedCount }})</span>
                            </button>
                        @endcan
                    @endif
                @else
                    {{-- Deleted View: Back to All + Restore All --}}
                    @if($indexRoute)
                        <a href="{{ $indexRoute }}"
                           class="btn w-full sm:w-auto justify-center bg-slate-500 font-normal text-white hover:bg-slate-600 focus:bg-slate-600 active:bg-slate-600/90">
                            <i class="fa-solid fa-list mr-2 text-tiny sm:text-xs"></i>
                            <span class="text-tiny sm:text-xs">Lihat Semua</span>
                        </a>
                    @endif

                    @if($deletedCount > 0 && $restoreAllModalId)
                        <button type="button"
                                data-toggle="modal"
                                data-target="#{{ $restoreAllModalId }}"
                                class="btn w-full sm:w-auto justify-center bg-success font-normal text-white hover:bg-success/90 focus:bg-success/90 active:bg-success/80">
                            <i class="fa-solid fa-undo mr-2 text-tiny sm:text-xs"></i>
                            <span class="text-tiny sm:text-xs">Restore All ({{ $deletedCount }})</span>
                        </button>
                    @endif
                @endif
            @else
                {{-- Normal View: Deleted Records + Create --}}
                @if($hasDeletedView && $indexRoute)
                    @if(!empty($policyModel))
                        {{-- With Policy --}}
                        @can('viewAny', $policyModel)
                            <a href="{{ $indexRoute }}?view_deleted=1"
                               class="btn w-full sm:w-auto justify-center bg-slate-500 font-normal text-white hover:bg-slate-600 focus:bg-slate-600 active:bg-slate-600/90">
                                <i class="fa-solid fa-trash-alt mr-2 text-tiny sm:text-xs"></i>
                                <span class="text-tiny sm:text-xs">Deleted Records</span>
                            </a>
                        @endcan
                    @else
                        <a href="{{ $indexRoute }}?view_deleted=1"
                           class="btn w-full sm:w-auto justify-center bg-slate-500 font-normal text-white hover:bg-slate-600 focus:bg-slate-600 active:bg-slate-600/90">
                            <i class="fa-solid fa-trash-alt mr-2 text-tiny sm:text-xs"></i>
                            <span class="text-tiny sm:text-xs">Deleted Records</span>
                        </a>
                    @endif
                @endif

                @if($createRoute)
                    @if(!empty($policyModel))
                        {{-- With Policy --}}
                        @can('create', $policyModel)
                            <a href="{{ $createRoute }}"
                               class="btn w-full sm:w-auto justify-center bg-primary font-normal text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                <i class="fa-solid fa-plus mr-2 text-tiny sm:text-xs"></i>
                                <span class="text-tiny sm:text-xs">{{ $createText }}</span>
                            </a>
                        @endcan
                    @else
                        <a href="{{ $createRoute }}"
                           class="btn w-full sm:w-auto justify-center bg-primary font-normal text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                            <i class="fa-solid fa-plus mr-2 text-tiny sm:text-xs"></i>
                            <span class="text-tiny sm:text-xs">{{ $createText }}</span>
                        </a>
                    @endif
                @endif
            @endif
        </div>
    </div>
</div>