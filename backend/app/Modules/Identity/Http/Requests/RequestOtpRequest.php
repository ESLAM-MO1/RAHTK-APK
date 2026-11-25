<?php

declare(strict_types=1);

namespace App\Modules\Identity\Http\Requests;

use App\Modules\Identity\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class RequestOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:20'],
            'purpose' => ['required', 'string', 'in:login'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $phone = $this->input('phone');

                if (is_string($phone) && PhoneNumber::normalize($phone) === null) {
                    $validator->errors()->add('phone', __('errors.invalid_phone'));
                }
            },
        ];
    }
}
