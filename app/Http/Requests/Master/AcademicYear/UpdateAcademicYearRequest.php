<?php

namespace App\Http\Requests\Master\AcademicYear;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateAcademicYearRequest extends FormRequest
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
        $academicYearId = $this->route('academic_year')->id ?? null;

        return [
            'year_label' => [
                'required',
                'string',
                'regex:/^\d{4}\/\d{4}$/',
                'unique:academic_years,year_label,' . $academicYearId,
            ],
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'year_label' => 'Tahun Akademik',
            'start_date' => 'Tanggal Mulai',
            'end_date' => 'Tanggal Akhir',
            'is_active' => 'Status Aktif',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // General
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah terdaftar.',
            'string' => ':attribute harus berupa teks.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'boolean' => ':attribute harus berupa nilai boolean.',

            // Specific
            'year_label.regex' => 'Format :attribute harus YYYY/YYYY (contoh: 2024/2025).',
            'start_date.before' => 'Tanggal Mulai harus sebelum Tanggal Akhir.',
            'end_date.after' => 'Tanggal Akhir harus setelah Tanggal Mulai.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        // Flash notifikasi kustom ke sesi (untuk Toastify)
        session()->flash('notification_data', [
            'type' => 'error',
            // Mengambil pesan error pertama dari semua field
            'text' => $validator->errors()->first(),
            'position' => 'center-top',
            'duration' => 4000,
        ]);

        // Lanjutkan dengan penanganan validasi gagal bawaan Laravel
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
