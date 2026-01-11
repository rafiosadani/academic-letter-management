<?php

namespace App\Http\Requests\Letter;

use App\Enums\LetterType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class StoreLetterRequestRequest extends FormRequest
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
        $letterType = LetterType::tryFrom($this->input('letter_type'));

        if (!$letterType) {
            return [
                'letter_type' => ['required', Rule::enum(LetterType::class)],
            ];
        }

        $rules = [
            'letter_type' => ['required', Rule::enum(LetterType::class)],
        ];

        // Get form fields for letter type
        $formFields = $letterType->formFields();

        foreach ($formFields as $fieldName => $config) {
            $fieldRules = [];

            if ($config['required']) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($fieldName === 'parent_nip') {
                $fieldRules[] = 'regex:/^(\d{18}|-)$/';
            }

            // Add type-specific rules
            switch ($config['type']) {
                case 'text':
                case 'select':
                case 'select_or_other':
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

        // Document validation
        $requiredDocuments = $letterType->requiredDocuments();
        if (count($requiredDocuments) > 0) {
            $rules['documents'] = ['array'];
        }
        foreach ($requiredDocuments as $key => $config) {
            $docRules = [];

            if ($config['required'] && !$this->isMethod('put') && !$this->isMethod('patch')) {
                $docRules[] = 'required';
            } else {
                $docRules[] = 'nullable';
            }

            $docRules[] = 'file';
            $docRules[] = 'max:' . ($config['max_size'] ?? 5120);

            if (!empty($config['types'])) {
                $docRules[] = 'mimes:' . implode(',', $config['types']);
            }

            $rules["documents.{$key}"] = $docRules;
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $letterType = LetterType::tryFrom($this->input('letter_type'));

            if ($letterType) {
                $formFields = $letterType->formFields();
                foreach ($formFields as $fieldName => $config) {
                    if ($config['type'] === 'student_list' && $this->has($fieldName)) {
                        $this->validateStudentList($validator, $fieldName);
                    }

                    if ($fieldName === 'parent_nip' && $this->filled($fieldName)) {
                        $nip = $this->input($fieldName);
                        if ($nip !== '-' && !preg_match('/^\d{18}$/', $nip)) {
                            $validator->errors()->add($fieldName, 'NIP harus berupa 18 digit angka atau tanda (-) jika tidak ada.');
                        }
                    }
                }
            }
        });
    }

    private function validateStudentList($validator, $fieldName)
    {
        $studentListJson = $this->input($fieldName);
        $studentList = json_decode($studentListJson, true);

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
            $validator->errors()->add($fieldName, 'Maksimal 50 mahasiswa dapat ditambahkan.');
            return;
        }

        foreach ($studentList as $index => $student) {
            $studentNumber = $index + 1;

            if (!isset($student['name']) || empty(trim($student['name']))) {
                $validator->errors()->add($fieldName, "Nama mahasiswa ke-{$studentNumber} harus diisi.");
            }

            if (!isset($student['nim']) || empty(trim($student['nim']))) {
                $validator->errors()->add($fieldName, "NIM mahasiswa ke-{$studentNumber} harus diisi.");
            }

            if (!isset($student['program']) || empty(trim($student['program']))) {
                $validator->errors()->add($fieldName, "Program Studi mahasiswa ke-{$studentNumber} harus diisi.");
            }

            if (isset($student['nim']) && !empty($student['nim'])) {
                if (!preg_match('/^\d{15}$/', $student['nim'])) {
                    $validator->errors()->add($fieldName, "NIM mahasiswa ke-{$studentNumber} harus berupa 15 digit angka.");
                }
            }

            $nims = array_column($studentList, 'nim');
            if (count($nims) !== count(array_unique($nims))) {
                $validator->errors()->add($fieldName, "Terdapat NIM yang sama dalam daftar mahasiswa.");
            }
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'letter_type.required' => ':attribute harus dipilih',
            'letter_type.enum' => ':attribute tidak valid',

            // Parent info
//            'parent_name.required' => ':attribute harus diisi',
//            'parent_nip.required' => ':attribute harus diisi',
//            'parent_rank.required' => ':attribute harus diisi',
//            'parent_institution.required' => ':attribute harus diisi',
//            'parent_institution_address.required' => ':attribute harus diisi',

            'required' => ':attribute wajib diisi',
            'string' => ':attribute harus berupa teks',
            'max' => ':attribute maksimal :max karakter',
            'date' => ':attribute harus berupa tanggal yang valid',
            'date_format' => ':attribute harus berupa waktu yang valid (HH:MM)',
            'json' => ':attribute harus berformat JSON yang valid',

            // Student List
            'student_list.required' => ':attribute harus diisi',
            'student_list.json' => ':attribute harus berformat JSON yang valid',

            // Documents
            'documents.*.required' => ':attribute wajib diunggah',
            'documents.*.file' => ':attribute harus berupa file',
            'documents.*.mimes' => ':attribute harus berformat :values',
            'documents.*.max' => ':attribute maksimal berukuran :max KB',
        ];
    }

    /**
     * Get custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attributes = [
            'letter_type' => 'Jenis Surat',

            // Parent info
//            'parent_name' => 'Nama Orang Tua',
//            'parent_nip' => 'NIP Orang Tua',
//            'parent_rank' => 'Pangkat/Golongan Orang Tua',
//            'parent_institution' => 'Instansi Orang Tua',
//            'parent_institution_address' => 'Alamat Instansi Orang Tua',

            'student_list' => 'Daftar Mahasiswa',
            'documents' => 'Dokumen Pendukung',
        ];

        $letterType = LetterType::tryFrom($this->input('letter_type'));

        if ($letterType) {
            foreach ($letterType->formFields() as $fieldName => $config) {
                $attributes[$fieldName] = $config['label'] ?? ucwords(str_replace('_', ' ', $fieldName));
            }

            foreach ($letterType->requiredDocuments() as $key => $config) {
                $attributes["documents.$key"] = $config['label'] ?? ucfirst(str_replace('_', ' ', $key));
            }
        }

        return $attributes;
    }

    /**
     * Handle a failed validation attempt.
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

        // Flashing ke session agar bisa dibaca oleh komponen toast/notifikasi Anda
        session()->flash('notification_data', $notificationArray);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
