@props([
    'id' => null,
    'label',
    'name',
    'options' => [],
    'value' => '',
    'placeholder' => 'Pilih opsi...',
    'required' => false,
    'helper' => null,
    'multiple' => false,
    'disabled' => false,
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
            {{-- {{ $required ? 'required' : '' }} --}}
            {{ $multiple ? 'multiple' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge(['class' => 'form-select mt-1.5 w-full w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary disabled:pointer-events-none disabled:select-none disabled:border-none disabled:bg-zinc-100 dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent dark:disabled:bg-navy-600']) }}
    >
        @if($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $optionValue => $optionLabel)
            @php
                $labelToDisplay = is_object($optionLabel) ? ($optionLabel->name ?? $optionLabel->title ?? '') : $optionLabel;
                $oldValue = old($name, $value);
            @endphp

            <option
                    value="{{ $optionValue }}"
                    {{ (is_array($oldValue) ? in_array($optionValue, $oldValue) : $oldValue == $optionValue) ? 'selected' : '' }}
            >
                {{ $labelToDisplay }}
            </option>
        @endforeach
    </select>

    @if($disabled)
        @php
            $currentValue = old($name, $value);
            $fieldName = $multiple ? "{$name}[]" : $name;
        @endphp

        @if($multiple)
            @foreach((array)$currentValue as $v)
                <input type="hidden" name="{{ $fieldName }}" value="{{ $v }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $fieldName }}" value="{{ $currentValue }}">
        @endif
    @endif

    @error($name)
        <span class="text-tiny-plus text-error mt-1 ms-1">{{ $message }}</span>
    @enderror

    {{-- @if($helper)--}}
    {{--    <span class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1 ms-1">--}}
    {{--        {{ $helper }}--}}
    {{--    </span>--}}
    {{-- @endif--}}

    @if(!$errors->has($name) && $helper)
        <span class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1 ms-1">
            {{ $helper }}
        </span>
    @endif
</label>