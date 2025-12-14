<?php

namespace App\Http\Requests\Setting\General;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingRequest extends FormRequest
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
            // General settings
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,ico', 'max:512'],

            // Header settings
            'header_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'header_ministry' => ['nullable', 'string', 'max:500'],
            'header_university' => ['nullable', 'string', 'max:255'],
            'header_faculty' => ['nullable', 'string', 'max:255'],
            'header_address' => ['nullable', 'string', 'max:500'],
            'header_phone' => ['nullable', 'string', 'max:50'],
            'header_fax' => ['nullable', 'string', 'max:50'],
            'header_email' => ['nullable', 'email', 'max:255'],
            'header_website' => ['nullable', 'url', 'max:255'],

            // Footer settings
            'footer_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'footer_text' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // General
            'site_name.string' => 'Nama situs harus berupa teks.',
            'site_name.max' => 'Nama situs maksimal 255 karakter.',

            'site_logo.image' => 'Logo situs harus berupa gambar.',
            'site_logo.mimes' => 'Logo situs harus berformat PNG, JPG, atau JPEG.',
            'site_logo.max' => 'Ukuran logo situs maksimal 2MB.',

            'favicon.image' => 'Favicon harus berupa gambar.',
            'favicon.mimes' => 'Favicon harus berformat PNG atau ICO.',
            'favicon.max' => 'Ukuran favicon maksimal 512KB.',

            // Header
            'header_logo.image' => 'Logo header harus berupa gambar.',
            'header_logo.mimes' => 'Logo header harus berformat PNG, JPG, atau JPEG.',
            'header_logo.max' => 'Ukuran logo header maksimal 2MB.',

            'header_ministry.string' => 'Nama kementerian harus berupa teks.',
            'header_ministry.max' => 'Nama kementerian maksimal 500 karakter.',

            'header_university.string' => 'Nama universitas harus berupa teks.',
            'header_university.max' => 'Nama universitas maksimal 255 karakter.',

            'header_faculty.string' => 'Nama fakultas harus berupa teks.',
            'header_faculty.max' => 'Nama fakultas maksimal 255 karakter.',

            'header_address.string' => 'Alamat harus berupa teks.',
            'header_address.max' => 'Alamat maksimal 500 karakter.',

            'header_phone.string' => 'Nomor telepon harus berupa teks.',
            'header_phone.max' => 'Nomor telepon maksimal 50 karakter.',

            'header_fax.string' => 'Nomor fax harus berupa teks.',
            'header_fax.max' => 'Nomor fax maksimal 50 karakter.',

            'header_email.email' => 'Format email tidak valid.',
            'header_email.max' => 'Email maksimal 255 karakter.',

            'header_website.url' => 'Format website tidak valid.',
            'header_website.max' => 'Website maksimal 255 karakter.',

            // Footer
            'footer_logo.image' => 'Logo footer harus berupa gambar.',
            'footer_logo.mimes' => 'Logo footer harus berformat PNG, JPG, atau JPEG.',
            'footer_logo.max' => 'Ukuran logo footer maksimal 2MB.',

            'footer_text.string' => 'Teks footer harus berupa teks.',
            'footer_text.max' => 'Teks footer maksimal 1000 karakter.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'site_name' => 'Nama Situs',
            'site_logo' => 'Logo Situs',
            'favicon' => 'Favicon',
            'header_logo' => 'Logo Header',
            'header_ministry' => 'Nama Kementerian',
            'header_university' => 'Nama Universitas',
            'header_faculty' => 'Nama Fakultas',
            'header_address' => 'Alamat',
            'header_phone' => 'Nomor Telepon',
            'header_fax' => 'Nomor Fax',
            'header_email' => 'Email',
            'header_website' => 'Website',
            'footer_logo' => 'Logo Footer',
            'footer_text' => 'Teks Footer',
        ];
    }
}
