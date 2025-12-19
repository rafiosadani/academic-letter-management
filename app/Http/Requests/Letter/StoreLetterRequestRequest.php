<?php

namespace App\Http\Requests\Letter;

use App\Enums\LetterType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

        // Add parent info validation for SKAK Tunjangan
        if ($letterType === LetterType::SKAK_TUNJANGAN) {
            $rules['parent_name'] = ['required', 'string', 'max:255'];
            $rules['parent_nip'] = ['required', 'string', 'max:30'];
            $rules['parent_rank'] = ['required', 'string', 'max:255'];
            $rules['parent_institution'] = ['required', 'string', 'max:255'];
            $rules['parent_institution_address'] = ['required', 'string'];
        }

        // Document validation
        $requeiredDocuments = $letterType->requiredDocuments();
        foreach ($requeiredDocuments as $key => $config) {
            $docRules = [];

            if ($config['required']) {
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
            'parent_name.required' => ':attribute harus diisi',
            'parent_nip.required' => ':attribute harus diisi',
            'parent_rank.required' => ':attribute harus diisi',
            'parent_institution.required' => ':attribute harus diisi',
            'parent_institution_address.required' => ':attribute harus diisi',

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
            'parent_name' => 'Nama Orang Tua',
            'parent_nip' => 'NIP Orang Tua',
            'parent_rank' => 'Pangkat/Golongan Orang Tua',
            'parent_institution' => 'Instansi Orang Tua',
            'parent_institution_address' => 'Alamat Instansi Orang Tua',

            'keperluan' => 'Keperluan',
            'keterangan' => 'Keterangan',
            'judul_penelitian' => 'Judul Penelitian',
            'nama_tempat_penelitian' => 'Nama Tempat Penelitian',
            'alamat_tempat_penelitian' => 'Alamat Tempat Penelitian',
            'no_hp' => 'Nomor HP',
            'dosen_pembimbing' => 'Dosen Pembimbing',
            'bulan_pelaksanaan' => 'Bulan Pelaksanaan',
            'nama_instansi_tujuan' => 'Nama Instansi Tujuan',
            'jabatan_penerima' => 'Jabatan Penerima',
            'alamat_instansi' => 'Alamat Instansi',
            'alasan_dispensasi' => 'Alasan Dispensasi',
            'posisi_magang' => 'Posisi Magang',
            'keperluan_detail' => 'Keperluan Detail',
            'tanggal_mulai' => 'Tanggal Mulai',
            'tanggal_selesai' => 'Tanggal Selesai',
            'nama_kegiatan' => 'Nama Kegiatan',
            'tanggal_kegiatan' => 'Tanggal Kegiatan',
            'waktu_mulai' => 'Waktu Mulai',
            'waktu_selesai' => 'Waktu Selesai',
            'tempat_kegiatan' => 'Tempat Kegiatan',
        ];

        $letterType = LetterType::tryFrom($this->input('letter_type'));

        if ($letterType) {
            foreach ($letterType->requiredDocuments() as $key => $config) {
                $attributes["documents.$key"] = $config['label'] ?? ucfirst(str_replace('_', ' ', $key));
            }
        }

        return $attributes;
    }
}
