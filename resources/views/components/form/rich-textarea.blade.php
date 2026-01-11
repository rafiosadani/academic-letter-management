@props([
    'label',
    'name',
    'value' => '',
    'placeholder' => 'Tulis konten di sini...',
    'required' => false,
    'helper' => null,
    'height' => '200px',
    'toolbar' => 'full',
])

@php
    $editorId = $name . '_editor_' . uniqid();
@endphp

<label class="block">
    <span class="font-medium text-slate-600 dark:text-navy-100">
        {{ $label }}
        @if($required)
            <span class="text-error">*</span>
        @endif
    </span>

    <div class="mt-1.5">
        <div
                id="{{ $editorId }}"
                data-quill-editor="{{ $name }}"
                data-quill-toolbar="{{ $toolbar }}"
                style="height: {{ $height }}"
                class="rounded-lg border border-slate-300 bg-white dark:border-navy-450 dark:bg-navy-700"
        ></div>

        <textarea
                name="{{ $name }}"
                id="{{ $name }}"
                {{ $required ? 'required' : '' }}
                style="display: none;"
        >{{ old($name, $value) }}</textarea>
    </div>

    @error($name)
    <span class="text-tiny+ text-error mt-1 block">{{ $message }}</span>
    @enderror

    @if($helper)
        <span class="text-xs text-slate-400 dark:text-navy-300 mt-1 block">{{ $helper }}</span>
    @endif
</label>