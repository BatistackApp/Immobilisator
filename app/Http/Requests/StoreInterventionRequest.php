<?php

namespace App\Http\Requests;

use App\Enums\InterventionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInterventionRequest extends FormRequest
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
            'asset_id' => ['required', 'exists:assets,id'],
            'provider_id' => ['nullable', 'exists:providers,id'],
            'type' => ['required', Rule::enum(InterventionType::class)],
            'title' => ['required', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'min:0'],
            'intervention_date' => ['required', 'date'],
            'is_capitalized' => ['required', 'boolean'],
        ];
    }
}
