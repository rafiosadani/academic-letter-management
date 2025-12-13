<?php

namespace App\Http\Requests\Setting\LetterNumberConfig;

use App\Enums\LetterType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLetterNumberConfigRequest extends FormRequest
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
        $letterNumberConfig = $this->route('letter_number_config');

        return [
            'letter_type' => [
                'required',
                'string',
                Rule::in(array_column(LetterType::cases(), 'value')),
                Rule::unique('letter_number_configs', 'letter_type')->ignore($letterNumberConfig->id)
            ],
            'prefix' => ['required', 'string', 'max:50'],
            'code' => ['required', 'string', 'max:10', 'alpha_dash'],
            'padding' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'letter_type.required' => 'Jenis surat harus dipilih.',
            'letter_type.unique' => 'Konfigurasi untuk jenis surat ini sudah ada.',
            'prefix.required' => 'Prefix harus diisi.',
            'prefix.max' => 'Prefix maksimal 50 karakter.',
            'code.required' => 'Kode surat harus diisi.',
            'code.max' => 'Kode surat maksimal 10 karakter.',
            'code.alpha_dash' => 'Kode surat hanya boleh berisi huruf, angka, dash, dan underscore.',
            'padding.required' => 'Padding harus diisi.',
            'padding.integer' => 'Padding harus berupa angka.',
            'padding.min' => 'Padding minimal 1 digit.',
            'padding.max' => 'Padding maksimal 10 digit.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'letter_type' => 'Jenis Surat',
            'prefix' => 'Prefix',
            'code' => 'Kode Surat',
            'padding' => 'Padding',
        ];
    }
}
