<?php

namespace App\Http\Requests\Approval;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RejectRequest extends FormRequest
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
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'reason.required' => ':attribute harus diisi',
            'reason.max' => ':attribute maksimal :max karakter',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'reason' => 'Alasan Penolakan',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $notifications = collect($validator->errors()->all())->map(fn ($error) => [
            'type' => 'error',
            'text' => $error,
            'position' => 'center-top',
            'duration' => 3000,
        ])->toArray();

//        $allErrors = $validator->errors()->all();
//
//        $notificationArray = [];
//        foreach ($allErrors as $error) {
//            $notificationArray[] = [
//                'type' => 'error',
//                'text' => $error,
//                'position' => 'center-top',
//                'duration' => 3000,
//            ];
//        }

        session()->flash('notification_data', $notifications);

        session()->flash('open_reject_modal_id', $this->route('approval')->id);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
