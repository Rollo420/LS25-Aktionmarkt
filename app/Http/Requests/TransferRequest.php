<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'to_account' => 'required|string|exists:banks,iban',
            'amount' => 'required|integer|min:1|max:4294967295',
        ];
    }

    public function messages(): array
    {
        return [
            'to_account.exists' => 'Die angegebene IBAN ist nicht gültig.',
            'amount.max' => 'Der Betrag darf maximal 4.294.967.295 betragen.',
        ];
    }
}
