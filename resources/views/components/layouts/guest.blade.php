@props([
    'title' => $title,
    'cardTitle' => $cardTitle,
    'cardSubtitle' => $cardSubtitle,
])

<x-layouts.base :title="$title" bodyClass="is-header-blur">

    {{-- Pass styles dari child ke base --}}
    @isset($styles)
        <x-slot:styles>
            {{ $styles }}
        </x-slot:styles>
    @endisset

    <!-- Page Wrapper -->
    <div id="root" class="min-h-100vh cloak flex grow bg-slate-50 dark:bg-navy-900">
        <!-- Main Content Wrapper -->
        <main class="grid w-full grow grid-cols-1 place-items-center">
            <div class="w-full max-w-[26rem] p-4 sm:px-5">
                <div class="text-center">
                    <img class="mx-auto size-28 transition-transform duration-500 ease-in-out hover:scale-110 hover:rotate-[12deg]"
                         src="{{ asset('assets/images/logo/vokasi-ub.png') }}"
                         alt="logo"
                    />
                    @if($cardTitle)
                        <div class="mt-4">
                            <h2 class="text-2xl font-semibold text-slate-600 dark:text-navy-100">
                                {{ $cardTitle }}
                            </h2>
                            @if($cardSubtitle)
                                <p class="text-slate-400 dark:text-navy-300">
                                    {{ $cardSubtitle }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
                {{ $slot }}
                <div class="mt-8 flex justify-center text-xs text-slate-400 dark:text-navy-300">
                    <a href="#">Privacy Notice</a>
                    <div class="mx-3 my-1 w-px bg-slate-200 dark:bg-navy-500"></div>
                    <a href="#">Term of service</a>
                </div>
            </div>
        </main>
    </div>

    {{-- Pass scripts dari child ke base --}}
    @isset($scripts)
        <x-slot:scripts>
            {{ $scripts }}
        </x-slot:scripts>
    @endisset

</x-layouts.base>
