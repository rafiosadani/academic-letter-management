<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UploadFinalPdfRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'final_pdf' => ['required', 'file', 'mimes:pdf', 'max:5120'], // 5MB
            'letter_number' => ['required', 'string', 'max:100', 'unique:letter_requests,letter_number'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'final_pdf.required' => 'File PDF wajib diupload.',
            'final_pdf.file' => 'File yang diupload harus berupa file.',
            'final_pdf.mimes' => 'File harus berformat PDF.',
            'final_pdf.max' => 'Ukuran file maksimal 5MB.',
            'letter_number.required' => 'Nomor surat harus diisi.',
            'letter_number.max' => 'Nomor surat maksimal 100 karakter.',
            'letter_number.unique' => 'Nomor surat sudah digunakan oleh pengajuan lain.',
            'note.max' => 'Catatan maksimal 500 karakter.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'final_pdf' => 'PDF Final',
            'letter_number' => 'Nomor Surat',
            'note' => 'Catatan',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $notifications = collect($validator->errors()->all())->map(fn ($error) => [
            'type' => 'error',
            'text' => $error,
            'position' => 'center-top',
            'duration' => 3000,
        ])->toArray();

        session()->flash('notification_data', $notifications);

        $letterId = $this->route('letter');
        $id = is_object($letterId) ? $letterId->id : $letterId;

        session()->flash('open_upload_final_pdf_modal_id', $id);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
