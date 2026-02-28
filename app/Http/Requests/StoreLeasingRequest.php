<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeasingRequest extends FormRequest
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
            'asset_id' => [
                'required',
                'exists:assets,id',
                Rule::unique('leasings')->ignore($this->route('leasing')),
            ],
            'provider_id' => ['required', 'exists:providers,id'],
            'contract_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('leasings')->ignore($this->route('leasing')),
            ],
            'monthly_rent' => ['required', 'numeric', 'min:0'],
            'purchase_option_price' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'option_exercised' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_id.unique' => 'Cet actif est déjà lié à un contrat de crédit-bail actif.',
            'contract_number.unique' => 'Ce numéro de contrat de leasing existe déjà.',
        ];
    }
}
