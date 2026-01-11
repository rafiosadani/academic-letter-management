<?php

namespace App\Http\Requests\Approval;

use App\Enums\LetterType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class EditContentRequest extends FormRequest
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
        $approval = $this->route('approval');
        $letterType = $approval->letterRequest->letter_type;
        $rules = [];

        // Get form fields for letter type
        foreach ($letterType->formFields() as $fieldName => $config) {
            $fieldRules = [];

            if ($config['required']) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Add type-specific rules
            switch ($config['type']) {
                case 'text':
                case 'select':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:255';
                    break;
                case 'textarea':
                    $fieldRules[] = 'string';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'time':
                    $fieldRules[] = 'date_format:H:i';
                    break;
                case 'student_list':
                    $fieldRules[] = 'json';
                    break;
            }

            $rules[$fieldName] = $fieldRules;
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $approval = $this->route('approval');
            $letterType = $approval->letterRequest->letter_type;

            foreach ($letterType->formFields() as $fieldName => $config) {
                if ($config['type'] === 'student_list' && $this->has($fieldName)) {
                    $this->validateStudentList($validator, $fieldName);
                }
            }
        });
    }

    private function validateStudentList($validator, $fieldName)
    {
        $jsonString = $this->input($fieldName);
        $studentList = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $validator->errors()->add($fieldName, 'Format daftar mahasiswa tidak valid.');
            return;
        }

        if (!is_array($studentList)) {
            $validator->errors()->add($fieldName, 'Daftar mahasiswa harus berupa array.');
            return;
        }

        if (count($studentList) < 1) {
            $validator->errors()->add($fieldName, 'Minimal 1 mahasiswa harus ditambahkan.');
            return;
        }

        if (count($studentList) > 50) {
            $validator->errors()->add($fieldName, 'Maksimal 50 mahasiswa.');
            return;
        }

        foreach ($studentList as $index => $student) {
            $no = $index + 1;

            if (empty($student['name'])) {
                $validator->errors()->add($fieldName, "Nama mahasiswa ke-{$no} harus diisi.");
            }

            if (empty($student['nim'])) {
                $validator->errors()->add($fieldName, "NIM mahasiswa ke-{$no} harus diisi.");
            }

            if (empty($student['program'])) {
                $validator->errors()->add($fieldName, "Program Studi mahasiswa ke-{$no} harus dipilih.");
            }

            if (!empty($student['nim']) && !preg_match('/^\d{15}$/', $student['nim'])) {
                $validator->errors()->add($fieldName, "NIM mahasiswa ke-{$no} harus 15 digit angka.");
            }
        }

        $nims = array_column($studentList, 'nim');
        if (count($nims) !== count(array_unique($nims))) {
            $validator->errors()->add($fieldName, 'Terdapat NIM yang sama dalam daftar.');
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            '*.required' => ':attribute harus diisi',
            '*.string' => ':attribute harus berupa teks',
            '*.max' => ':attribute maksimal :max karakter',
            '*.date' => ':attribute harus berupa tanggal yang valid',
            '*.date_format' => ':attribute harus berupa waktu yang valid (HH:MM)',
            '*.json' => ':attribute harus berformat JSON yang valid',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        $approval = $this->route('approval');
        $letterType = $approval->letterRequest->letter_type;

        $attributes = [];
        foreach ($letterType->formFields() as $fieldName => $config) {
            $attributes[$fieldName] = $config['label'] ?? ucwords(str_replace('_', ' ', $fieldName));
        }

        return $attributes;
    }

    protected function failedValidation(Validator $validator)
    {
        $this->flash();

        $notifications = collect($validator->errors()->all())->map(fn ($error) => [
            'type' => 'error',
            'text' => $error,
            'position' => 'center-top',
            'duration' => 3000,
        ])->toArray();

        // Flash notifikasi ke session
        session()->flash('notification_data', $notifications);

        // Flash ID modal agar otomatis terbuka (gunakan nama session yang berbeda agar tidak bentrok)
        session()->flash('open_edit_modal_id', $this->route('approval')->id);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
