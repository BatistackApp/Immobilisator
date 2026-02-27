<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'vat_number',
        'address',
        'postal_code',
        'city',
        'country',
        'fiscal_year_start_month',
        'amortization_options',
    ];

    protected function casts(): array
    {
        return [
            'amortization_options' => 'array',
        ];
    }
}
