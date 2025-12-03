<?php

namespace App\Rules;

use App\Enums\DegreeEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidDegree implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!in_array($value, DegreeEnum::values(), true)) {
            $validValues = implode(', ', DegreeEnum::values());
            $fail("Jenjang yang dipilih tidak valid. Pilihan yang tersedia: {$validValues}");
        }
    }
}
