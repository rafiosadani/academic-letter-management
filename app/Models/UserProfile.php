<?php

namespace App\Models;

use App\Models\User;
use App\Models\StudyProgram;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'student_or_employee_id',
        'phone',
        'photo',
        'study_program_id',
        'address',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // ==========================================================
    // RELATIONSHIPS
    // ==========================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    // ==========================================================
    // ACCESSORS (MUTATORS)
    // ==========================================================

    /**
     * Check if profile is complete for basic letters.
     */
    public function isCompleteForBasicLetters(): bool
    {
        return !empty($this->place_of_birth)
            && !empty($this->date_of_birth);
    }

    /**
     * Check if profile is complete for SKAK Tunjangan.
     */
    public function isCompleteForSkakTunjangan(): bool
    {
        return $this->isCompleteForBasicLetters()
            && !empty($this->parent_name)
            && !empty($this->parent_nip)
            && !empty($this->parent_rank)
            && !empty($this->parent_institution)
            && !empty($this->parent_institution_address);
    }

    /**
     * Get missing fields for basic letters.
     */
    public function getMissingFieldsForBasicLetters(): array
    {
        $missing = [];

        if (empty($this->place_of_birth)) {
            $missing[] = 'Tempat Lahir';
        }

        if (empty($this->date_of_birth)) {
            $missing[] = 'Tanggal Lahir';
        }

        if (empty($this->student_or_employee_id)) {
            $missing[] = 'NIM';
        }

        if (empty($this->study_program_id)) {
            $missing[] = 'Program Studi';
        }

        return $missing;
    }

    /**
     * Get missing fields for SKAK Tunjangan.
     */
    public function getMissingFieldsForSkakTunjangan(): array
    {
        $missing = $this->getMissingFieldsForBasicLetters();

        if (empty($this->parent_name)) {
            $missing[] = 'Nama Orang Tua';
        }

        if (empty($this->parent_nip)) {
            $missing[] = 'NIP Orang Tua';
        }

        if (empty($this->parent_rank)) {
            $missing[] = 'Pangkat/Golongan Orang Tua';
        }

        if (empty($this->parent_institution)) {
            $missing[] = 'Nama Instansi Orang Tua';
        }

        if (empty($this->parent_institution_address)) {
            $missing[] = 'Alamat Instansi Orang Tua';
        }

        return $missing;
    }

    /**
     * Get formatted date of birth (Indonesian format).
     */
    public function getFormattedDateOfBirthAttribute(): ?string
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->translatedFormat('d F Y');
    }

    /**
     * Get full birth info (Place, Date).
     */
    public function getFullBirthInfoAttribute(): ?string
    {
        if (!$this->place_of_birth || !$this->date_of_birth) {
            return null;
        }

        return $this->place_of_birth . ', ' . $this->formatted_date_of_birth;
    }

    protected function shortName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $fullName = $this->full_name;
                if (!$fullName) {
                    return 'User';
                }

                $parts = explode(' ', $fullName);
                $numParts = count($parts);

                if ($numParts <= 2) {
                    return $fullName;
                }

                $firstName = array_shift($parts);
                $secondName = array_shift($parts);

                $initialsArray = collect($parts)
                    ->map(fn($part) => strtoupper(substr($part, 0, 1) . '.'))
                    ->all(); // Contoh: ['C.', 'F.']

                $initialsString = implode(' ', $initialsArray);

                $initialsString = rtrim($initialsString, '. ');

                $displayName = $firstName . ' ' . $secondName;
                if ($initialsString) {
                    $displayName .= ' ' . $initialsString;
                }

                return trim($displayName);
            }
        );
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->photo) {
                    if ($this->photo === 'default.png') {
                        return asset('assets/images/default.png');
                    } else {
                        return asset('storage/' . $this->photo);
                    }
                }

                return asset('assets/images/default.png');
            }
        );
    }
}
