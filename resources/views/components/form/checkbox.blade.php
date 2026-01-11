@props([
    'label',
    'name',
    'value' => '1',
    'checked' => false,
    'helper' => null
])

<label class="inline-flex items-center space-x-2">
    <input
            type="checkbox"
            name="{{ $name }}"
            value="{{ $value }}"
            {{ old($name, $checked) ? 'checked' : '' }}
            {{ $attributes->merge(['class' => 'form-checkbox is-basic h-5 w-5 rounded border-slate-400/70 checked:bg-primary checked:border-primary dark:border-navy-400 dark:checked:bg-accent dark:checked:border-accent']) }}
    />
    <span class="text-slate-600 dark:text-navy-100 text-sm">
        {{ $label }}
        @if($helper)
            <span class="text-xs text-slate-400 dark:text-navy-300 block">{{ $helper }}</span>
        @endif
    </span>
</label>