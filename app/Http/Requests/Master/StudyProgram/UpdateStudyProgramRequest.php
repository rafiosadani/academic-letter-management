<?php

namespace App\Http\Requests\Master\StudyProgram;

use App\Rules\ValidDegree;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateStudyProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize degree to uppercase
        $this->merge([
            'degree' => $this->degree ? strtoupper($this->degree) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studyProgramId = $this->route('study_program')->id ?? null;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:study_programs,name,' . $studyProgramId,
            ],
            'degree' => ['required', 'string', new ValidDegree()],
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
            'name' => 'Nama Program Studi',
            'degree' => 'Jenjang',
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
            'max' => ':attribute maksimal :max karakter.',
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