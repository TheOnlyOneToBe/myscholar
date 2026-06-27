<?php

namespace Modules\Config\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.school_year.create');
    }

    public function rules(): array
    {
        return [
            'year' => ['required', 'integer', 'min:1900', 'max:2100', 'unique:school_years'],
            'label' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date', 'date_format:Y-m-d', 'after:start_date'],
            'is_active' => ['boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'year.unique' => 'Une année scolaire pour cette année existe déjà.',
            'end_date.after' => 'La date de fin doit être après la date de début.',
        ];
    }
}
