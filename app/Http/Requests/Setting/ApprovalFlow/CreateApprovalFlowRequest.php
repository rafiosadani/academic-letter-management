<?php

namespace App\Http\Requests\Setting\ApprovalFlow;

use App\Enums\ApprovalAction;
use App\Enums\LetterType;
use App\Enums\OfficialPosition;
use App\Models\ApprovalFlow;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateApprovalFlowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Policy handles authorization
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'letter_type' => ['required', Rule::enum(LetterType::class)],
            'step' => ['required', 'integer', 'min:1'],
            'step_label' => ['required', 'string', 'max:100'],
            'required_positions' => ['required', 'array', 'min:1'],
            'required_positions.*' => ['required', Rule::enum(OfficialPosition::class)],
            'can_edit_content' => ['nullable', 'boolean'],
            'is_editable' => ['nullable', 'boolean'],
            'on_reject' => ['nullable', Rule::enum(ApprovalAction::class)],
            'is_final' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $letterType = $this->letter_type ? LetterType::from($this->letter_type) : null;

            if (!$letterType) {
                return;
            }

            $existingStep = ApprovalFlow::where('letter_type', $this->letter_type)
                ->where('step', $this->step)
                ->first();

            if ($existingStep) {
                $validator->errors()->add(
                    'step',
                    "Step {$this->step} sudah digunakan untuk jenis surat ini. Silakan pilih nomor step lain."
                );
            }

            if ($this->boolean('is_final')) {
                $hasFinalStep = ApprovalFlow::where('letter_type', $this->letter_type)
                    ->where('is_final', true)
                    ->exists();

                if ($hasFinalStep) {
                    $validator->errors()->add(
                        'is_final',
                        'Sudah ada step final untuk jenis surat ini. Hanya boleh 1 step final per jenis surat.'
                    );
                }
            }

            // 3. For Word format
            if ($letterType->isExternal()) {
                // Auto-set to false for Word format
                $this->merge([
                    'can_edit_content' => false,
                    'is_editable' => false,
                ]);

                if (!$this->on_reject) {
                    $this->merge(['on_reject' => ApprovalAction::TO_STUDENT->value]);
                }
            } else {
                // For non-Word format (PDF), on_reject is required
                if (!$this->on_reject) {
                    $validator->errors()->add(
                        'on_reject',
                        'Aksi saat ditolak harus dipilih.'
                    );
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'letter_type.required' => 'Jenis surat harus dipilih.',
            'step.required' => 'Nomor step harus diisi.',
            'step.integer' => 'Nomor step harus berupa angka.',
            'step.min' => 'Nomor step minimal 1.',
            'step_label.required' => 'Label step harus diisi.',
            'step_label.max' => 'Label step maksimal 100 karakter.',
            'required_positions.required' => 'Minimal harus memilih 1 jabatan.',
            'required_positions.array' => 'Format jabatan tidak valid.',
            'required_positions.min' => 'Minimal harus memilih 1 jabatan.',
            'required_positions.*.required' => 'Jabatan tidak boleh kosong.',
            'on_reject.required' => 'Aksi saat ditolak harus dipilih.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'letter_type' => 'Jenis Surat',
            'step' => 'Nomor Step',
            'step_label' => 'Label Step',
            'required_positions' => 'Jabatan yang Diperlukan',
            'required_positions.*' => 'Jabatan',
            'can_edit_content' => 'Boleh Edit Konten',
            'is_editable' => 'Mahasiswa Boleh Edit',
            'on_reject' => 'Aksi Saat Ditolak',
            'is_final' => 'Step Final',
        ];
    }
}
