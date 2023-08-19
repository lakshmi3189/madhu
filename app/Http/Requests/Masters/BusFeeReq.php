<?php

namespace App\Http\Requests\Masters;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BusFeeReq extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "studentId" => "required|integer",
            "classId" => "required|integer",
            "availedFrom" => "required|date|date_format:Y-m-d",
            "availedTo" => "required|date|date_format:Y-m-d",
            "destination" => "required|string",
            "destinationKm" => "required|numeric",
            "busFee" => "required|numeric",
        ];
    }

    /**
     * | Validation Failed Function
     */

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            responseMsgs(
                false,
                $validator->errors(),
                "Validation Error",
                "010101",
                "1.0",
                responseTime(),
                "POST",
                $this->deviceId
            )
        );
    }
}
