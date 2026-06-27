<?php

namespace Modules\Config\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSystemSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.settings.edit');
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'regex:/^[a-z0-9_]+$/', 'unique:system_settings'],
            'value' => ['nullable'],
            'type' => ['required', 'in:string,integer,boolean,json'],
            'group' => ['required', 'string', 'regex:/^[a-z0-9_]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.unique' => 'Ce paramètre existe déjà.',
            'key.regex' => 'La clé ne doit contenir que des lettres minuscules, chiffres et underscores.',
            'group.regex' => 'Le groupe ne doit contenir que des lettres minuscules, chiffres et underscores.',
        ];
    }
}
