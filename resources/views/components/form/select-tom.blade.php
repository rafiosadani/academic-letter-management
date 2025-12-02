@props([
    'label',
    'name',
    'options' => [],
    'value' => '',
    'placeholder' => 'Pilih opsi...',
    'required' => false,
    'helper' => null,
    'multiple' => false,
    'searchable' => true,
    'creatable' => false,
    'tomSelect' => true,
])

<label class="block">
    <span class="font-medium text-slate-600 dark:text-navy-100">
        {{ $label }}
        @if($required)
            <span class="text-error">*</span>
        @endif
    </span>

    <select
            name="{{ $name }}{{ $multiple ? '[]' : '' }}"
            {{ $required ? 'required' : '' }}
            {{ $multiple ? 'multiple' : '' }}
            data-tom-select="{{ $tomSelect ? 'true' : 'false' }}"
            data-tom-searchable="{{ $searchable ? 'true' : 'false' }}"
            data-tom-creatable="{{ $creatable ? 'true' : 'false' }}"
            {{ $attributes->merge(['class' => 'form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent']) }}
    >
        @if($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $optionValue => $optionLabel)
            <option
                    value="{{ $optionValue }}"
                    {{ old($name, $value) == $optionValue ? 'selected' : '' }}
            >
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
    <span class="text-tiny+ text-error mt-1 block">{{ $message }}</span>
    @enderror

    @if($helper)
        <span class="text-xs text-slate-400 dark:text-navy-300 mt-1 block">{{ $helper }}</span>
    @endif
</label>