<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
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
            // User credentials
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],

            // User profile
            'full_name' => ['required', 'string', 'max:255'],
            'student_or_employee_id' => ['required', 'string', 'unique:user_profiles,student_or_employee_id', 'max:50', 'regex:/^[0-9]+$/'],
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
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
            'email' => 'Email',
            'password' => 'Password',
            'full_name' => 'Nama Lengkap',
            'student_or_employee_id' => 'NIM',
            'study_program_id' => 'Program Studi',
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
            'integer' => ':attribute harus berupa angka.',
            'max' => ':attribute maksimal :max karakter.',
            'exists' => ':attribute yang dipilih tidak valid.',

            // Specific
            'email.email' => 'Format email tidak valid.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal :min karakter.',
            'student_or_employee_id.regex' => 'NIM harus berupa angka.',
            'student_or_employee_id.unique' => 'NIM sudah terdaftar dalam sistem.',
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

        session()->flash('notification_data', $notificationArray);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
