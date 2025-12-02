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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class, 'study_program_id');
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

                // Jika tidak ada foto, gunakan default avatar dengan initial/nama lengkap
                $name = $this->full_name ?? 'User';

                // Gunakan URL ui-avatars.com untuk default avatar
//                return "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=random&color=fff&size=200";
                return asset('assets/images/default.png');
            }
        );
    }
}
