<?php

namespace App\Http\Requests\Letter;

class UpdateLetterRequestRequest extends StoreLetterRequestRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Use same validation as store
        return parent::rules();
    }
}
