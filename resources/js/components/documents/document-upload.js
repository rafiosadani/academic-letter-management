/**
 * Document Upload Handler using FilePond
 *
 * Handles file upload for supporting documents in letter requests.
 * Uses FilePond library (already included in Lineone template).
 */

/**
 * Initialize document upload for all filepond inputs
 */
export function initDocumentUpload() {
    const filepondInputs = document.querySelectorAll('.filepond-input');

    if (filepondInputs.length === 0) {
        return;
    }

    filepondInputs.forEach(input => {
        const letterRequestId = input.dataset.letterRequestId;
        const maxFiles = parseInt(input.dataset.maxFiles) || 5;

        // Skip if already initialized
        if (input._filepond) {
            return;
        }

        // FilePond configuration
        const config = {
            // Max files
            maxFiles: maxFiles,

            // Accepted file types
            acceptedFileTypes: [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
            ],

            // Max file size (10MB)
            maxFileSize: '10MB',

            // Labels (Indonesian)
            // labelIdle: `
            //     <div class="flex flex-col items-center justify-center py-6">
            //         <i class="fa-solid fa-cloud-arrow-up text-4xl text-slate-400 dark:text-navy-300 mb-3"></i>
            //         <p class="text-tiny text-slate-600 dark:text-navy-100">
            //             <span class="font-semibold">Klik untuk upload</span> atau drag & drop
            //         </p>
            //         <p class="text-tiny text-slate-400 dark:text-navy-300 mt-1">
            //             PDF, DOCX, JPG, PNG (Max 10MB)
            //         </p>
            //     </div>
            // `,
            labelFileTypeNotAllowed: 'Tipe file tidak valid',
            fileValidateTypeLabelExpectedTypes: 'Format: PDF, DOCX, JPG, PNG',
            labelFileProcessing: 'Mengupload...',
            labelFileProcessingComplete: 'Upload selesai',
            labelFileProcessingAborted: 'Upload dibatalkan',
            labelFileProcessingError: 'Error saat upload',
            labelTapToCancel: 'klik untuk batalkan',
            labelTapToRetry: 'klik untuk coba lagi',
            labelTapToUndo: 'klik untuk undo',
            labelButtonRemoveItem: 'Hapus',
            labelButtonAbortItemLoad: 'Batalkan',
            labelButtonRetryItemLoad: 'Coba lagi',
            labelButtonAbortItemProcessing: 'Batalkan',
            labelButtonUndoItemProcessing: 'Undo',
            labelButtonRetryItemProcessing: 'Coba lagi',
            labelButtonProcessItem: 'Upload',

            // Server configuration
            server: {
                process: (fieldName, file, metadata, load, error, progress, abort) => {
                    // Create form data
                    const formData = new FormData();
                    formData.append('files[]', file);
                    formData.append('letter_request_id', letterRequestId);

                    // CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                    // Create request
                    const request = new XMLHttpRequest();
                    request.open('POST', '/documents/upload');

                    // Track progress
                    request.upload.onprogress = (e) => {
                        progress(e.lengthComputable, e.loaded, e.total);
                    };

                    // Handle success
                    request.onload = function() {
                        if (request.status >= 200 && request.status < 300) {
                            const response = JSON.parse(request.responseText);

                            // Show success notification
                            if (window.$notification) {
                                window.$notification({
                                    text: response.message,
                                    variant: 'success',
                                    position: 'center-top',
                                    duration: 3000,
                                });
                            }

                            load(request.responseText);
                        } else {
                            const response = JSON.parse(request.responseText);
                            error(response.message || 'Upload gagal');
                        }
                    };

                    // Handle error
                    request.onerror = function() {
                        error('Network error');
                    };

                    // Set headers
                    request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    request.setRequestHeader('Accept', 'application/json');

                    // Send request
                    request.send(formData);

                    // Return abort function
                    return {
                        abort: () => {
                            request.abort();
                            abort();
                        }
                    };
                },

                // Revert (undo) - optional
                revert: null,
            },

            // Styling
            stylePanelLayout: 'compact',
            styleButtonRemoveItemPosition: 'right',

            // Events
            onprocessfile: (error, file) => {
                if (!error) {
                    console.log('File uploaded successfully:', file.filename);

                    // Reload page after 2 seconds to show updated document list
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            },

            onerror: (error) => {
                console.error('FilePond error:', error);

                // Show error notification
                if (window.$notification) {
                    window.$notification({
                        text: error.main || 'Terjadi kesalahan saat upload',
                        variant: 'error',
                        position: 'center-top',
                        duration: 4000,
                    });
                }
            },
        };

        // Create FilePond instance
        input._filepond = FilePond.create(input, config);
    });
}