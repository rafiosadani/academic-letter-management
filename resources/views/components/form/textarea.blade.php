@props([
    'label' => '',
    'name',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'helper' => null,
    'rows' => 4,
])

<label class="block">
    @if($label)
        <span class="font-medium text-slate-600 dark:text-navy-100">
            {{ $label }}
            @if($required)
                <span class="text-error">*</span>
            @endif
        </span>
    @endif

    <textarea
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
{{--        {{ $required ? 'required' : '' }}--}}
        {{ $attributes->merge(['class' => 'form-textarea mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent']) }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <span class="text-tiny-plus text-error mt-1 block">{{ $message }}</span>
    @enderror

    @if($helper)
        <span class="text-tiny-plus text-slate-500 dark:text-navy-300 ms-1 mt-1 block">{{ $helper }}</span>
    @endif
</label>