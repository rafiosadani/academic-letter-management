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
        'place_of_birth',
        'date_of_birth',
        'student_or_employee_id',
        'phone',
        'photo',
        'study_program_id',
        'address',
        'parent_name',
        'parent_nip',
        'parent_rank',
        'parent_institution',
        'parent_institution_address',
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
                if (!$fullName) return 'User';

                // 1. Identifikasi Gelar Depan (Dr., Prof., Ir., dsb)
                // Mencari kata yang diakhiri titik di awal kalimat
                $prefixPattern = '/^(Dr\.|Prof\.|Ir\.|Drs\.|H\.|Hj\.)\s+/i';
                $prefixes = [];
                while (preg_match($prefixPattern, $fullName, $matches)) {
                    $prefixes[] = $matches[1];
                    $fullName = preg_replace($prefixPattern, '', $fullName);
                }

                // 2. Identifikasi Gelar Belakang (setelah koma, misal: , S.T., M.Sc.)
                $parts = explode(',', $fullName);
                $nameWithInitials = array_shift($parts); // Ambil bagian sebelum koma pertama (Nama Inti)
                $suffixes = $parts; // Sisanya adalah gelar belakang

                // 3. Olah Nama Inti (Singkat nama ke-3 dst menjadi inisial)
                $nameParts = explode(' ', trim($nameWithInitials));
                $numParts = count($nameParts);

                if ($numParts > 2) {
                    $firstName = array_shift($nameParts); // Nama 1
                    $secondName = array_shift($nameParts); // Nama 2

                    // Singkat sisanya menjadi inisial (A. B. C.)
                    $initials = collect($nameParts)
                        ->map(fn($part) => strtoupper(substr($part, 0, 1)) . '.')
                        ->implode(' ');

                    $finalName = "{$firstName} {$secondName} {$initials}";
                } else {
                    $finalName = $nameWithInitials;
                }

                // 4. Gabungkan Kembali: Prefix + Nama Hasil Olah + Suffix
                $prefixString = count($prefixes) > 0 ? implode(' ', $prefixes) . ' ' : '';
                $suffixString = count($suffixes) > 0 ? ', ' . implode(',', $suffixes) : '';

                return trim($prefixString . $finalName . $suffixString);
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
