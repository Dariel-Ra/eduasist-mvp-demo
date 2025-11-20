<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuardianStudentRequest extends FormRequest
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
            'relationship' => [
                'required',
                'string',
                'in:father,mother,guardian,other',
            ],
            'is_primary' => [
                'nullable',
                'boolean',
            ],
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
            'relationship' => 'tipo de relación',
            'is_primary' => 'contacto primario',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'relationship.required' => 'El tipo de relación es obligatorio.',
            'relationship.in' => 'El tipo de relación debe ser: padre, madre, tutor u otro.',
            'is_primary.boolean' => 'El contacto primario debe ser verdadero o falso.',
        ];
    }
}
