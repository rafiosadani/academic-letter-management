@props([
    'letterRequestId' => null,
    'maxFiles' => 5,
    'required' => false,
    'label' => 'Upload Dokumen Pendukung',
    'helper' => 'Format: PDF, DOCX, JPG, PNG (Max 10MB per file)',
])

<div class="document-upload-wrapper">
    <label class="block">
        <span class="font-medium text-slate-600 dark:text-navy-100">
            {{ $label }}
            @if($required)
                <span class="text-error">*</span>
            @endif
        </span>

        @if($helper)
            <p class="mt-1 text-xs text-slate-400 dark:text-navy-300">
                {{ $helper }}
            </p>
        @endif

        <div class="mt-3">
            <div class="filepond fp-bordered fp-filled">
                <input
                        type="file"
                        class="filepond-input"
                        data-letter-request-id="{{ $letterRequestId }}"
                        data-max-files="{{ $maxFiles }}"
                        multiple
                        {{ $required ? 'required' : '' }}
                />
            </div>
        </div>
    </label>

    @error('files')
    <span class="text-tiny+ text-error mt-1 block">{{ $message }}</span>
    @enderror

    @error('files.*')
    <span class="text-tiny+ text-error mt-1 block">{{ $message }}</span>
    @enderror
</div>