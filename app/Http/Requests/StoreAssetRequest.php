<?php

namespace App\Http\Requests;

use App\Enums\AmortizationMethod;
use App\Enums\AssetStatus;
use App\Enums\FundingType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
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
            'asset_category_id' => ['required', 'exists:asset_categories,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'provider_id' => ['nullable', 'exists:providers,id'],
            'reference' => [
                'required',
                'string',
                'max:50',
                Rule::unique('assets')->ignore($this->route('asset')),
            ],
            'designation' => ['required', 'string', 'max:255'],
            'funding_type' => ['required', Rule::enum(FundingType::class)],
            'acquisition_value' => ['required', 'numeric', 'min:0'],
            'salvage_value' => ['nullable', 'numeric', 'min:0', 'lt:acquisition_value'],
            'acquisition_date' => ['required', 'date'],
            'service_date' => ['required', 'date', 'after_or_equal:acquisition_date'],
            'useful_life' => ['required', 'integer', 'min:1', 'max:100'],
            'amortization_method' => ['required', Rule::enum(AmortizationMethod::class)],
            'status' => ['required', Rule::enum(AssetStatus::class)],
            'metadata' => ['nullable', 'array'],
            'gross_value_opening' => ['nullable', 'numeric', 'min:0'],
            'accumulated_depreciation_opening' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_date.after_or_equal' => "La date de mise en service ne peut pas être antérieure à la date d'acquisition.",
            'salvage_value.lt' => "La valeur résiduelle doit être inférieure à la valeur d'acquisition.",
            'reference.unique' => "Cette référence d'immobilisation est déjà utilisée.",
        ];
    }
}
