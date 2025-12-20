<?php

namespace App\Http\Requests\Approval;

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
            }

            $rules[$fieldName] = $fieldRules;
        }

        return $rules;
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
