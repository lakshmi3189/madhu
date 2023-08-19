<?php

namespace App\Http\Requests\Masters;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SchoolMasterReq extends FormRequest
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
            "firstName" => "required|string",
            "middleName" => "required|string",
            "lastName" => "required|string",
            "mobile" => "required|digits:10",
            "email" => "required|email",
            "password" => "required|string",
            "address" => "required|string",
            "schoolCode" => "required|string",
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
