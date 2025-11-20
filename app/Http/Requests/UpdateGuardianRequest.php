<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuardianRequest extends FormRequest
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
            'personal_email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\d\s\-\+\(\)]+$/',
            ],
            'whatsapp_number' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\d\s\-\+\(\)]+$/',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'personal_email.email' => 'El email personal debe ser válido.',
            'phone_number.regex' => 'El número de teléfono tiene un formato inválido.',
            'whatsapp_number.regex' => 'El número de WhatsApp tiene un formato inválido.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'personal_email' => 'email personal',
            'phone_number' => 'teléfono',
            'whatsapp_number' => 'WhatsApp',
        ];
    }
}
