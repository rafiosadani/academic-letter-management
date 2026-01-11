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
    'previewVariant' => 'fit', // 🔥 SINGLE PROP
])

@php
    $inputId = $name . '_' . uniqid();
    $previewId = $inputId . '_preview';
    $isImage = str_starts_with($accept, 'image') || $accept === '*/*';

    $alignClass = $centered ? 'text-center' : 'text-left';
    $buttonWrapperClass = $centered ? 'flex justify-center' : 'flex justify-start';
    $previewAlignClass = $centered ? 'flex justify-center' : '';
    $buttonWidthClass = $centered ? 'w-48' : 'w-full sm:w-auto';

    // 🔥 PREVIEW PRESET (1 PROP ONLY)
    $previewPresets = [
        'fit' => [
            'mode' => 'fit',
            'maxWidth' => 320,
            'aspect' => null,
        ],
        'square' => [
            'mode' => 'fixed',
            'width' => 120,
            'aspect' => '1 / 1',
        ],
        'landscape' => [
            'mode' => 'fixed',
            'width' => 240,
            'aspect' => '16 / 9',
        ],
        'portrait' => [
            'mode' => 'fixed',
            'width' => 180,
            'aspect' => '3 / 4',
        ],
        'small' => [
            'mode' => 'fit',
            'maxWidth' => 200,
            'aspect' => null,
        ],
        'medium' => [
            'mode' => 'fit',
            'maxWidth' => 320,
            'aspect' => null,
        ],
        'large' => [
            'mode' => 'fit',
            'maxWidth' => 480,
            'aspect' => null,
        ],
    ];

    $preset = $previewPresets[$previewVariant] ?? $previewPresets['fit'];
@endphp

<label class="block w-full">
    @if($label)
        <span class="font-medium text-slate-600 dark:text-navy-100 block {{ $alignClass }}">
            {{ $label }}
            @if($required)<span class="text-error">*</span>@endif
        </span>
    @endif

    <div class="mt-1.5 w-full {{ $buttonWrapperClass }}">
        <label
                id="{{ $inputId }}_btn_label"
                class="btn relative font-medium text-white shadow-lg transition-colors duration-200 {{ $buttonClass }} {{ $buttonWidthClass }}"
        >
            <input
                    tabindex="-1"
                    type="file"
                    id="{{ $inputId }}"
                    name="{{ $name }}{{ $multiple ? '[]' : '' }}"
                    accept="{{ $accept }}"
                    {{ $required ? 'required' : '' }}
                    {{ $multiple ? 'multiple' : '' }}
                    data-preview="true"
                    data-preview-target="{{ $previewId }}"
                    data-file-button="true"
                    data-default-text="{{ $buttonText }}"
                    data-change-text="{{ $changeText }}"

                    {{-- 🔥 PREVIEW CONFIG --}}
                    data-preview-mode="{{ $preset['mode'] }}"
                    data-preview-width="{{ $preset['width'] ?? '' }}"
                    data-preview-max-width="{{ $preset['maxWidth'] ?? '' }}"
                    data-preview-aspect="{{ $preset['aspect'] ?? '' }}"

                    class="pointer-events-none absolute inset-0 h-full w-full opacity-0"
            />

            <div class="flex items-center space-x-2">
                <i id="{{ $inputId }}_btn_icon" class="{{ $iconClass }}"></i>
                <span id="{{ $inputId }}_btn_text">{{ $buttonText }}</span>
            </div>
        </label>
    </div>

    {{-- Preview --}}
    @if($showPreview)
        <div id="{{ $previewId }}" class="mt-3 hidden {{ $previewAlignClass }}"></div>
    @endif

    @error($name)
        <span class="text-tiny-plus text-error mt-1 block {{ $alignClass }}">{{ $message }}</span>
    @enderror

    @if($helper)
        <span class="text-xs text-slate-400 dark:text-navy-300 mt-2 block {{ $alignClass }}">{{ $helper }}</span>
{{--    @else--}}
{{--        <span class="text-xs text-slate-400 dark:text-navy-300 mt-2 block {{ $alignClass }}">--}}
{{--            Format: {{ $accept === 'image/*' ? 'JPG, PNG, GIF' : ($accept === 'application/pdf' ? 'PDF' : 'Semua file') }}--}}
{{--                @if($multiple) | Bisa pilih multiple files @endif--}}
{{--        </span>--}}
    @endif
</label>

