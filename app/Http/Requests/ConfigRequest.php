<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'volatility_range' => 'required|numeric|min:0|max:1',
            'seasonal_effect_strength' => 'required|numeric|min:0|max:1',
            'crash_probability_monthly' => 'required|numeric|min:0|max:100',
            'crash_interval_months' => 'required|integer|min:1',
            'rally_probability_monthly' => 'required|numeric|min:0|max:100',
            'rally_interval_months' => 'required|integer|min:1',
        ];
    }
}
