<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
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
        $studentId = $this->route('student')->id;

        return [
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
            ],
            'enrollment_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('students', 'enrollment_code')->ignore($studentId),
                'regex:/^[A-Z0-9\-]+$/',
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],
            'grade_level' => [
                'nullable',
                'string',
                'max:50',
            ],
            'section' => [
                'nullable',
                'string',
                'max:20',
            ],
            'active' => [
                'boolean',
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
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'El apellido es obligatorio.',
            'enrollment_code.unique' => 'Este código de matrícula ya está en uso.',
            'enrollment_code.regex' => 'El código de matrícula solo puede contener letras mayúsculas, números y guiones.',
            'date_of_birth.date' => 'La fecha de nacimiento debe ser válida.',
            'date_of_birth.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
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
            'first_name' => 'nombre',
            'last_name' => 'apellido',
            'enrollment_code' => 'código de matrícula',
            'date_of_birth' => 'fecha de nacimiento',
            'grade_level' => 'grado',
            'section' => 'sección',
            'active' => 'activo',
        ];
    }
}
