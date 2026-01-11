<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = auth()->user();
        $isMahasiswa = $user->hasRole('Mahasiswa');

        return [
            // User table
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],

            // User profile - Personal Info
            'full_name' => ['required', 'string', 'max:255'],
            'place_of_birth' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // 2MB
            'address' => ['nullable', 'string', 'max:500'],

            'student_or_employee_id' => [
                $isMahasiswa ? 'nullable' : 'required',
                'string',
                'max:50'
            ],

            'study_program_id' => [
                $isMahasiswa ? 'required' : 'nullable',
                'integer',
                'exists:study_programs,id'
            ],

            // User profile - Parent Info (Optional, tapi required untuk SKAK Tunjangan)
            // Hanya divalidasi jika usernya Mahasiswa
            'parent_name' => [$isMahasiswa ? 'nullable' : 'prohibited', 'string', 'max:255'],
            'parent_nip' => [
                $isMahasiswa ? 'nullable' : 'prohibited',
                'string',
                'regex:/^(\d{18}|-)$/'
            ],
            'parent_rank' => [$isMahasiswa ? 'nullable' : 'prohibited', 'string', 'max:255'],
            'parent_institution' => [$isMahasiswa ? 'nullable' : 'prohibited', 'string', 'max:255'],
            'parent_institution_address' => [$isMahasiswa ? 'nullable' : 'prohibited', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $isMahasiswa = auth()->user()->hasRole('Mahasiswa');

        return [
            'email' => 'Email',
            'full_name' => 'Nama Lengkap',
            'place_of_birth' => 'Tempat Lahir',
            'date_of_birth' => 'Tanggal Lahir',
            'student_or_employee_id' => $isMahasiswa ? 'NIM' : 'NIP / NIK',
            'phone' => 'Nomor Telepon',
            'photo' => 'Foto Profil',
            'study_program_id' => 'Program Studi',
            'address' => 'Alamat',
            'parent_name' => 'Nama Orang Tua',
            'parent_nip' => 'NIP Orang Tua',
            'parent_rank' => 'Pangkat / Golongan Orang Tua',
            'parent_institution' => 'Nama Instansi Orang Tua',
            'parent_institution_address' => 'Alamat Instansi Orang Tua',
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
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'date_of_birth.date' => 'Format tanggal lahir tidak valid.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
            'parent_nip.regex' => 'NIP Orang Tua harus 18 digit angka atau tanda (-) jika tidak memiliki NIP.',
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
        $allErrors = $validator->errors()->all();

        $notificationArray = [];
        foreach ($allErrors as $error) {
            $notificationArray[] = [
                'type' => 'error',
                'text' => $error,
                'position' => 'center-top',
                'duration' => 3000,
            ];
        }

        session()->flash('active_tab', 'tab-account');

        session()->flash('notification_data', $notificationArray);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
