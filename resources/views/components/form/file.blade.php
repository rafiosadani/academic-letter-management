@props([
    'label' => null,
    'name',
    'required' => false,
    'helper' => null,
    'accept' => '*/*',
    'multiple' => false,
    'showPreview' => true,
    'centered' => false,
    'buttonText' => 'Choose File',
    'buttonClass' => 'bg-primary hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90',
    'iconClass' => 'fa-solid fa-cloud-arrow-up text-base',
    'currentUrl' => null,
    'currentFilename' => null,
    'changeText' => 'Change File',
])

@php
    $inputId = $name . '_' . uniqid();
    $previewId = $inputId . '_preview';
    $isImage = str_starts_with($accept, 'image') || $accept === '*/*';

    // Dynamic alignment classes
    $alignClass = $centered ? 'text-center' : 'text-left';
    $buttonWrapperClass = $centered ? 'flex justify-center' : 'flex justify-start';
    $previewAlignClass = $centered ? 'flex justify-center' : '';
    $buttonWidthClass = $centered ? 'w-48' : 'w-full sm:w-auto';
@endphp

<label class="block w-full">
    @if($label)
        <span class="font-medium text-slate-600 dark:text-navy-100 block w-full {{ $alignClass }}">
            {{ $label }}
            @if($required)
                <span class="text-error">*</span>
            @endif
        </span>
    @endif

    <div class="mt-1.5 w-full {{ $buttonWrapperClass }}">
        <label
            id="{{ $inputId }}_btn_label"
            {{ $attributes->merge([
                'class' =>
                    'btn relative font-medium text-white shadow-lg transition-colors duration-200 ' .
                    $buttonClass . ' ' . $buttonWidthClass
            ]) }}
        >
            <input
                tabindex="-1"
                type="file"
                id="{{ $inputId }}"
                name="{{ $name }}{{ $multiple ? '[]' : '' }}"
                accept="{{ $accept }}"
                {{ $required ? 'required' : '' }}
                {{ $multiple ? 'multiple' : '' }}

                @if($showPreview)
                    data-preview="true"
                    data-preview-target="{{ $previewId }}"
                @endif

                data-file-button="true"
                data-default-text="{{ $buttonText }}"
                data-change-text="{{ $changeText }}"

                class="pointer-events-none absolute inset-0 h-full w-full opacity-0"
                {{ $attributes->except(['class']) }}
            />
            <div class="flex items-center space-x-2" {{ $attributes->merge(['class']) }}>
                <i id="{{ $inputId }}_btn_icon" class="{{ $iconClass }}"></i>
                <span id="{{ $inputId }}_btn_text">{{ $buttonText }}</span>
            </div>
        </label>

    </div>

    {{-- Current File Preview (Non-Image) --}}
    @if($currentUrl && !$isImage)
        <div class="mt-3 {{ $centered ? 'flex justify-center flex-col items-center' : '' }}">
            <p class="text-slate-600 dark:text-navy-100 text-sm mb-2">File saat ini:</p>
            <a href="{{ $currentUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                class="w-fit max-w-full btn bg-slate-200 text-slate-800 dark:bg-navy-600 dark:text-navy-100 hover:bg-slate-300 dark:hover:bg-navy-500"
            >
                <i class="fa-solid fa-file-export mr-2"></i>
                Preview ({{ $currentFilename ?? basename($currentUrl) }})
            </a>
        </div>
    @endif

    {{-- Preview Container --}}
    @if($showPreview)
        <div id="{{ $previewId }}" class="mt-3 hidden {{ $previewAlignClass }}">
            {{-- Konten diisi oleh JS --}}
        </div>
    @endif

    @error($name)
        <span class="text-tiny-plus text-error mt-1 block {{ $alignClass }}">{{ $message }}</span>
    @enderror

    @if($helper)
        <span class="text-xs text-slate-400 dark:text-navy-300 mt-2 block {{ $alignClass }}">{{ $helper }}</span>
    @else
{{--        <span class="text-xs text-slate-400 dark:text-navy-300 mt-2 block {{ $alignClass }}">--}}
{{--            Format: {{ $accept === 'image/*' ? 'JPG, PNG, GIF' : ($accept === 'application/pdf' ? 'PDF' : 'Semua file') }}--}}
{{--            @if($multiple) | Bisa pilih multiple files @endif--}}
{{--        </span>--}}
    @endif
</label>
