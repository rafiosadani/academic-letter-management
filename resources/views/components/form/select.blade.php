@props([
    'id' => null,
    'label',
    'name',
    'options' => [],   // DIHARAPKAN: Array Asosiatif [value => label]
    'value' => '',     // Nilai yang dipilih saat ini
    'placeholder' => 'Pilih opsi...',
    'required' => false,
    'helper' => null,
    'multiple' => false,
])

<label class="block" id="{{ $id }}">
    <span class="font-medium text-slate-600 dark:text-navy-100">
        {{ $label }}
        @if($required)
            <span class="text-error">*</span>
        @endif
    </span>

    <select
            @if($id) id="{{ $id }}" @endif
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
{{--            {{ $required ? 'required' : '' }}--}}
            {{ $multiple ? 'multiple' : '' }}

            {{-- Class Styling --}}
            {{ $attributes->merge(['class' => 'form-select mt-1.5 w-full w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent']) }}
    >
        @if($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        {{-- Logika Tunggal: Hanya memproses Array Asosiatif (Kasus Pluck/MapWithKeys) --}}
        @foreach($options as $optionValue => $optionLabel)
            @php
                // Pastikan label adalah string yang bisa ditampilkan
                $labelToDisplay = is_object($optionLabel) ? ($optionLabel->name ?? $optionLabel->title ?? '') : $optionLabel;

                // Tentukan nilai yang akan dibandingkan (nilai lama atau nilai default)
                $oldValue = old($name, $value);
            @endphp

            <option
                    value="{{ $optionValue }}"
                    {{-- Pengecekan untuk single select (==) atau multi select (in_array) --}}
                    {{ (is_array($oldValue) ? in_array($optionValue, $oldValue) : $oldValue == $optionValue) ? 'selected' : '' }}
            >
                {{ $labelToDisplay }}
            </option>
        @endforeach
    </select>

    @error($name)
        <span class="text-tiny-plus text-error mt-1 ms-1">{{ $message }}</span>
    @enderror

{{--    @if($helper)--}}
{{--        <span class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1 ms-1">--}}
{{--            {{ $helper }}--}}
{{--        </span>--}}
{{--    @endif--}}

    @if(!$errors->has($name) && $helper)
        <span class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1 ms-1">
            {{ $helper }}
        </span>
    @endif
</label>