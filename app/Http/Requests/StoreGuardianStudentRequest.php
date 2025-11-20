<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuardianStudentRequest extends FormRequest
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
            'guardian_id' => [
                'required',
                'integer',
                'exists:guardians,id',
            ],
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],
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
            'guardian_id' => 'tutor',
            'student_id' => 'estudiante',
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
            'guardian_id.required' => 'El tutor es obligatorio.',
            'guardian_id.integer' => 'El tutor debe ser un número válido.',
            'guardian_id.exists' => 'El tutor seleccionado no existe.',
            'student_id.required' => 'El estudiante es obligatorio.',
            'student_id.integer' => 'El estudiante debe ser un número válido.',
            'student_id.exists' => 'El estudiante seleccionado no existe.',
            'relationship.required' => 'El tipo de relación es obligatorio.',
            'relationship.in' => 'El tipo de relación debe ser: padre, madre, tutor u otro.',
            'is_primary.boolean' => 'El contacto primario debe ser verdadero o falso.',
        ];
    }
}
