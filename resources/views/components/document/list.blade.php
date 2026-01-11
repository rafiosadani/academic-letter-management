@props([
    'documents' => [],
    'canDelete' => false,
    'title' => 'Dokumen',
    'emptyMessage' => 'Belum ada dokumen yang diupload',
])

<div class="document-list-wrapper">
    @if($title)
        <div class="mb-3 flex items-center justify-between">
            <h4 class="text-sm font-medium text-slate-700 dark:text-navy-100">
                {{ $title }}
            </h4>
            <span class="text-xs text-slate-400 dark:text-navy-300">
                {{ count($documents) }} file(s)
            </span>
        </div>
    @endif

    @if(count($documents) > 0)
        <div class="space-y-2">
            @foreach($documents as $document)
                <div class="group flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 p-3 transition-colors hover:bg-slate-100 dark:border-navy-500 dark:bg-navy-600 dark:hover:bg-navy-500">

                    {{-- Left: File Info --}}
                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                        {{-- Icon --}}
                        <div class="flex-shrink-0">
                            <i class="fa-solid {{ $document->icon }} text-2xl"></i>
                        </div>

                        {{-- File Details --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 dark:text-navy-100 truncate">
                                {{ $document->file_name }}
                            </p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="text-xs text-slate-400 dark:text-navy-300">
                                    {{ $document->file_size_formatted }}
                                </span>
                                <span class="text-xs text-slate-300 dark:text-navy-400">•</span>
                                <span class="text-xs text-slate-400 dark:text-navy-300">
                                    {{ $document->category_label }}
                                </span>
                                @if($document->type_label)
                                    <span class="text-xs text-slate-300 dark:text-navy-400">•</span>
                                    <span class="text-xs text-slate-400 dark:text-navy-300">
                                        {{ $document->type_label }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Right: Actions --}}
                    <div class="flex items-center space-x-2 flex-shrink-0">
                        {{-- Preview (PDF only) --}}
                        @if(str_contains($document->mime_type, 'pdf'))
                            <a href="{{ route('documents.stream', $document) }}"
                                target="_blank"
                                class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                                title="Preview"
                            >
                                <i class="fa-solid fa-eye text-sm"></i>
                            </a>
                        @endif

                        {{-- Download --}}
                        <a href="{{ route('documents.download', $document) }}"
                            class="btn size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
                            title="Download"
                        >
                            <i class="fa-solid fa-download text-sm"></i>
                        </a>

                        {{-- Delete --}}
                        @if($canDelete)
                            <button
                                type="button"
                                data-toggle="modal"
                                data-target="#delete-document-modal-{{ $document->id }}"
                                class="btn size-8 rounded-full p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25"
                                title="Hapus"
                            >
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Delete Modals --}}
        @foreach($documents as $document)
                <form id="delete-document-form-{{ $document->id }}"
                    method="POST"
                    action="{{ route('documents.destroy', $document) }}"
                    class="hidden">
                    @csrf
                    @method('DELETE')
                </form>

                <x-modal.confirm
                    id="delete-document-modal-{{ $document->id }}"
                    title="⚠️ Hapus Dokumen?"
                    confirm-type="error"
                    confirm-text="Ya, Hapus!"
                    cancel-text="Batal"
                    form="delete-document-form-{{ $document->id }}"
                >
                    <x-slot:message>
                        <div class="space-y-3">
                            <p>Dokumen ini akan <strong class="text-error">DIHAPUS</strong> dari sistem.</p>

                            {{-- Document Info --}}
                            <div class="rounded-lg bg-slate-100 dark:bg-navy-600 p-3">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fa-solid {{ $document->icon }} text-xl"></i>
                                    <p class="font-medium text-slate-700 dark:text-navy-100 truncate">
                                        {{ $document->file_name }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2 text-xs text-slate-600 dark:text-navy-200">
                                    <span>{{ $document->file_size_formatted }}</span>
                                    <span>•</span>
                                    <span>{{ $document->category_label }}</span>
                                    @if($document->type_label)
                                        <span>•</span>
                                        <span>{{ $document->type_label }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Warning --}}
                            <div class="rounded-lg bg-warning/10 border border-warning/20 p-3">
                                <p class="text-xs text-slate-600 dark:text-navy-200">
                                    <i class="fa-solid fa-info-circle text-warning mr-1"></i>
                                    File akan dihapus dari sistem dan tidak dapat dikembalikan.
                                </p>
                            </div>
                        </div>
                    </x-slot:message>
                </x-modal.confirm>
        @endforeach
    @else
        <div class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 py-8 dark:border-navy-500 dark:bg-navy-600">
            <i class="fa-solid fa-folder-open text-4xl text-slate-300 dark:text-navy-400"></i>
            <p class="mt-3 text-sm text-slate-400 dark:text-navy-300">
                {{ $emptyMessage }}
            </p>
        </div>
    @endif
</div>