@props([
    'cancelUrl',
    'cancelText' => 'Batal',
    'submitText' => 'Simpan',
    'submitIcon' => 'fa-check',
    'cancelIcon' => 'fa-xmark',
    'submitType' => 'primary', // primary, warning, success, error, info
    'isUpdate' => false,
])

<div class="sticky bottom-0 z-10 bg-slate-50 dark:bg-navy-800 border-t border-slate-200 dark:border-navy-600 py-4 -mx-[var(--margin-x)] px-[var(--margin-x)] mt-auto -mb-8">
    <div class="flex items-center justify-end space-x-3">
        {{-- Cancel Button --}}
        <a  href="{{ $cancelUrl }}"
            class="btn min-w-[7rem] border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500"
        >
            <i class="fa-solid {{ $cancelIcon }} mr-2"></i>
            {{ $cancelText }}
        </a>

        {{-- Submit Button --}}
        <button
            type="submit"
            class="btn min-w-[7rem] bg-{{ $submitType }} font-medium text-white hover:bg-{{ $submitType }}-focus focus:bg-{{ $submitType }}-focus active:bg-{{ $submitType }}-focus/90"
        >
            <i class="fa-solid {{ $submitIcon }} mr-2"></i>
            {{ $submitText }}
        </button>
    </div>
</div>