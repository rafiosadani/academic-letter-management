<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => 'required|array|max:5',
            'files.*' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,jpg,jpeg,png',
            ],
            'letter_request_id' => 'required|exists:letter_requests,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'files.required' => 'Silakan pilih file untuk diupload',
            'files.array' => 'Format upload tidak valid',
            'files.max' => 'Maksimal 5 file dapat diupload sekaligus',
            'files.*.required' => 'File harus dipilih',
            'files.*.file' => 'File tidak valid',
            'files.*.max' => 'Ukuran file maksimal 10MB',
            'files.*.mimes' => 'Format file harus: PDF, DOC, DOCX, JPG, atau PNG',
            'letter_request_id.required' => 'ID surat tidak ditemukan',
            'letter_request_id.exists' => 'Surat tidak ditemukan dalam sistem',
        ];
    }
}
