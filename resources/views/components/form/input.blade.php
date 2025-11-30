@props([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'helper' => null
])

<label class="block">
    <span class="font-medium text-slate-600 dark:text-navy-100">
        {{ $label }}
        @if($required)
            <span class="text-error">*</span>
        @endif
    </span>

    <input
            type="{{ $type }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent']) }}
    />

    @error($name)
        <span class="text-tiny-plus text-error mt-1 ms-1">{{ $message }}</span>
    @enderror

    @if(!$errors->has($name) && $helper)
        <span class="text-tiny-plus text-slate-500 dark:text-navy-300 mt-1 ms-1">
            {{ $helper }}
        </span>
    @endif
</label>