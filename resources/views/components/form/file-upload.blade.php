@props([
    'label',
    'name',
    'required' => false,
    'helper' => null,
    'multiple' => false,
    'accept' => 'image/*',
    'maxFileSize' => '5MB',
    'maxFiles' => null,
    'imagePreview' => true,
    'imageResize' => true,
    'resizeWidth' => 800,
    'resizeHeight' => 600,
    'instantUpload' => false,
    'allowImageCrop' => false,
])

@php
    $inputId = $name . '_filepond_' . uniqid();
@endphp

<label class="block">
    <span class="font-medium text-slate-600 dark:text-navy-100">
        {{ $label }}
        @if($required)
            <span class="text-error">*</span>
        @endif
    </span>

    <div class="mt-1.5">
        <input
                type="file"
                id="{{ $inputId }}"
                name="{{ $name }}{{ $multiple ? '[]' : '' }}"
                {{ $required ? 'required' : '' }}
                {{ $multiple ? 'multiple' : '' }}
                accept="{{ $accept }}"
                data-filepond="true"
                data-filepond-max-file-size="{{ $maxFileSize }}"
                @if($maxFiles) data-filepond-max-files="{{ $maxFiles }}" @endif
                data-filepond-image-preview="{{ $imagePreview ? 'true' : 'false' }}"
                data-filepond-image-resize="{{ $imageResize ? 'true' : 'false' }}"
                data-filepond-resize-width="{{ $resizeWidth }}"
                data-filepond-resize-height="{{ $resizeHeight }}"
                data-filepond-instant-upload="{{ $instantUpload ? 'true' : 'false' }}"
                data-filepond-allow-crop="{{ $allowImageCrop ? 'true' : 'false' }}"
                class="filepond"
        />
    </div>

    @error($name)
    <span class="text-tiny+ text-error mt-1 block">{{ $message }}</span>
    @enderror

    @if($helper)
        <span class="text-xs text-slate-400 dark:text-navy-300 mt-1 block">{{ $helper }}</span>
    @else
        <span class="text-xs text-slate-400 dark:text-navy-300 mt-1 block">
            Max size: {{ $maxFileSize }}
            @if($maxFiles) | Max files: {{ $maxFiles }} @endif
            @if($multiple) | Drag & drop untuk multiple files @endif
        </span>
    @endif
</label>