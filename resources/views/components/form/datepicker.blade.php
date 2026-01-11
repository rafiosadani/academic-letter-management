@props([
    'label',
    'name',
    'value' => '',
    'placeholder' => 'Pilih tanggal...',
    'required' => false,
    'helper' => null,
    'mode' => 'single',
    'enableTime' => false,
    'dateFormat' => 'Y-m-d',
    'minDate' => null,
    'maxDate' => null,
])

<label class="block">
    <span class="font-medium text-slate-600 dark:text-navy-100">
        {{ $label }}
        @if($required)
            <span class="text-error">*</span>
        @endif
    </span>

    <span class="relative mt-1.5 flex">
        <input
                type="text"
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
{{--                {{ $required ? 'required' : '' }}--}}
                autocomplete="off"
                data-flatpickr="true"
                data-flatpickr-mode="{{ $mode }}"
                data-flatpickr-enable-time="{{ $enableTime ? 'true' : 'false' }}"
                data-flatpickr-date-format="{{ $dateFormat }}"
                @if($minDate) data-flatpickr-min-date="{{ $minDate }}" @endif
                @if($maxDate) data-flatpickr-max-date="{{ $maxDate }}" @endif
                {{ $attributes->merge(['class' => 'form-input peer w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent']) }}
        />

        <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </span>
    </span>

{{--    @error($name)--}}
{{--    <span class="text-tiny+ text-error mt-1 block">{{ $message }}</span>--}}
{{--    @enderror--}}

{{--    @if($helper)--}}
{{--        <span class="text-xs text-slate-400 dark:text-navy-300 mt-1 block">{{ $helper }}</span>--}}
{{--    @endif--}}

    @error($name)
    <span class="text-tiny-plus text-error mt-1 ms-1">{{ $message }}</span>
    @enderror

    @if(!$errors->has($name) && $helper)
        <span class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1 ms-1">
            {{ $helper }}
        </span>
    @endif
</label>