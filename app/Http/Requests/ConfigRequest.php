<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->isAdministrator();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:configs,name,' . ($this->config?->id ?? 'NULL'),
            'description' => 'nullable|string|max:1000',
            'volatility_range' => 'required|numeric|min:0|max:1',
            'seasonal_effect_strength' => 'required|numeric|min:0|max:1',
            'crash_probability_monthly' => 'required|numeric|min:0|max:100',
            'crash_interval_months' => 'required|integer|min:1',
            'rally_probability_monthly' => 'required|numeric|min:0|max:100',
            'rally_interval_months' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Der Name ist erforderlich'),
            'name.unique' => __('Eine Config mit diesem Namen existiert bereits'),
            'volatility_range.min' => __('Die Volatilität muss mindestens 0 sein'),
            'volatility_range.max' => __('Die Volatilität darf höchstens 1 sein'),
            'seasonal_effect_strength.min' => __('Die saisonale Effektstärke muss mindestens 0 sein'),
            'seasonal_effect_strength.max' => __('Die saisonale Effektstärke darf höchstens 1 sein'),
            'crash_probability_monthly.min' => __('Die Crash-Wahrscheinlichkeit kann nicht negativ sein'),
            'crash_probability_monthly.max' => __('Die Crash-Wahrscheinlichkeit darf maximal 100% sein'),
            'rally_probability_monthly.min' => __('Die Rally-Wahrscheinlichkeit kann nicht negativ sein'),
            'rally_probability_monthly.max' => __('Die Rally-Wahrscheinlichkeit darf maximal 100% sein'),
            'crash_interval_months.min' => __('Das Crash-Intervall muss mindestens 1 Monat sein'),
            'crash_interval_months.required' => __('Das Crash-Intervall ist erforderlich'),
            'rally_interval_months.min' => __('Das Rally-Intervall muss mindestens 1 Monat sein'),
            'rally_interval_months.required' => __('Das Rally-Intervall ist erforderlich'),
        ];
    }
}
