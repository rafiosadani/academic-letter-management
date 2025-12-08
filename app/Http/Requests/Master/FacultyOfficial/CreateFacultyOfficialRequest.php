<?php

namespace App\Http\Requests\Master\FacultyOfficial;

use App\Enums\OfficialPosition;
use App\Models\FacultyOfficial;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFacultyOfficialRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $positionValues = array_column(OfficialPosition::cases(), 'value');

        return [
            'user_id' => ['required', 'exists:users,id'],
            'position' => ['required', Rule::in($positionValues)],
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Get position enum
            $position = $this->position ? OfficialPosition::from($this->position) : null;

            if (!$position) {
                return;
            }

            // Auto-clear study_program_id if position doesn't require it
            if (!$position->requiresStudyProgram()) {
                $this->merge(['study_program_id' => null]);
            }

            // 1. Validate Kaprodi must have study_program_id
            if ($position->requiresStudyProgram() && !$this->study_program_id) {
                $validator->errors()->add(
                    'study_program_id',
                    'Program Studi wajib diisi untuk posisi ' . $position->label() . '.'
                );
            }

            // 2. Validate study_program_id should match user's home program (warning/optional)
            if ($this->study_program_id && $this->user_id) {
                $user = User::with('profile')->find($this->user_id);

                if ($user && $user->profile && $user->profile->study_program_id) {
                    if ($user->profile->study_program_id != $this->study_program_id) {
                        // Optional: This can be warning or error based on business rule
                        // For now, we allow it but could add to errors if strict
                        // $validator->errors()->add(
                        //     'study_program_id',
                        //     'Program Studi tidak sesuai dengan home program user.'
                        // );
                    }
                }
            }

            // 3. Check for overlapping periods (same user + same position)
            if ($this->user_id && $this->position && $this->start_date) {
                $hasOverlap = FacultyOfficial::hasOverlap(
                    userId: $this->user_id,
                    position: $this->position,
                    startDate: $this->start_date,
                    endDate: $this->end_date
                );

                if ($hasOverlap) {
                    $validator->errors()->add(
                        'start_date',
                        'Periode jabatan bertumpuk dengan periode yang sudah ada untuk user dan posisi yang sama.'
                    );
                }
            }

            // 4. Check if user already has active assignment for this position
            if ($this->user_id && $this->position && !$this->end_date) {
                if (FacultyOfficial::hasActiveAssignment($this->user_id, $this->position)) {
                    $validator->errors()->add(
                        'end_date',
                        'User sudah memiliki jabatan aktif (tanpa end_date) untuk posisi ini. Hanya boleh 1 jabatan aktif per posisi.'
                    );
                }
            }

            // 5. Check if position must be unique (only 1 active globally or per study program)
            if ($position->isUnique()) {
                $hasActive = FacultyOfficial::hasActiveForPosition(
                    position: $this->position,
                    studyProgramId: $this->study_program_id
                );

                if ($hasActive) {
                    $positionLabel = $position->label();

                    if ($position === OfficialPosition::KETUA_PROGRAM_STUDI && $this->study_program_id) {
                        $validator->errors()->add(
                            'position',
                            "Sudah ada {$positionLabel} yang aktif untuk program studi ini. Hanya boleh 1 {$positionLabel} aktif per program studi."
                        );
                    } else {
                        $validator->errors()->add(
                            'position',
                            "Sudah ada {$positionLabel} yang aktif. Hanya boleh 1 {$positionLabel} aktif dalam satu waktu."
                        );
                    }
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
            'user_id.required' => 'User harus dipilih.',
            'user_id.exists' => 'User yang dipilih tidak valid.',
            'position.required' => 'Posisi jabatan harus dipilih.',
            'position.in' => 'Posisi jabatan tidak valid.',
            'study_program_id.exists' => 'Program Studi tidak valid.',
            'start_date.required' => 'Tanggal mulai harus diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'end_date.date' => 'Format tanggal selesai tidak valid.',
            'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'User',
            'position' => 'Posisi Jabatan',
            'study_program_id' => 'Program Studi',
            'start_date' => 'Tanggal Mulai',
            'end_date' => 'Tanggal Selesai',
            'notes' => 'Catatan',
        ];
    }
}
