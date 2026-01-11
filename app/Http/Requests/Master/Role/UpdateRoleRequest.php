<?php

namespace App\Http\Requests\Master\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
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
        $roleId = $this->route('role'); // Ambil ID dari route parameter

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($roleId)
            ],
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
            'is_editable' => 'nullable|boolean',
            'is_deletable' => 'nullable|boolean',
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
            'name' => 'Nama role',
            'permissions' => 'Hak akses',
            'permissions.*' => 'Hak akses',
            'is_editable' => 'Dapat diedit',
            'is_deletable' => 'Dapat dihapus',
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
            'name.required' => ':attribute wajib diisi.',
            'name.string' => ':attribute harus berupa teks.',
            'name.max' => ':attribute maksimal :max karakter.',
            'name.unique' => ':attribute sudah digunakan, silakan gunakan nama lain.',

            'permissions.required' => ':attribute wajib dipilih minimal 1.',
            'permissions.array' => ':attribute harus berupa array.',
            'permissions.min' => ':attribute wajib dipilih minimal :min.',
            'permissions.*.exists' => 'Salah satu :attribute tidak valid.',

            'is_editable.boolean' => ':attribute harus bernilai benar atau salah.',
            'is_deletable.boolean' => ':attribute harus bernilai benar atau salah.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_editable' => $this->has('is_editable') ? filter_var($this->is_editable, FILTER_VALIDATE_BOOLEAN) : false,
            'is_deletable' => $this->has('is_deletable') ? filter_var($this->is_deletable, FILTER_VALIDATE_BOOLEAN) : false,
        ]);
    }
}
