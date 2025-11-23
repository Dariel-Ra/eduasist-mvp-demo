<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return auth()->user()->can('update', $this->course);
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
            'name' => [
                'required',
                'string',
                'max:150',
            ],
 
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('courses', 'code')->ignore($this->course->id),
                'regex:/^[A-Z0-9-]+$/', // Solo mayúsculas, números y guiones
            ],
 
            'description' => [
                'nullable',
                'string',
            ],
 
            'grade_level' => [
                'nullable',
                'string',
                'max:50',
            ],
 
            'active' => [
                'nullable',
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
            'name.required' => 'El nombre del curso es obligatorio.',
            'name.max' => 'El nombre del curso no puede tener más de 150 caracteres.',
            'code.unique' => 'Este código ya está en uso por otro curso.',
            'code.regex' => 'El código solo puede contener letras mayúsculas, números y guiones.',
            'code.max' => 'El código no puede tener más de 50 caracteres.',
            'grade_level.max' => 'El nivel de grado no puede tener más de 50 caracteres.',
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
            'name' => 'nombre',
            'code' => 'código',
            'description' => 'descripción',
            'grade_level' => 'nivel de grado',
            'active' => 'estado activo',
        ];
    }
}
