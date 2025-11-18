<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Aquí puedes agregar lógica de autorización
        // Por ejemplo: return auth()->user()->can('update', $this->route('teacher'));
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $teacherId = $this->route('teacher')->id;

        return [
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('teachers', 'code')->ignore($teacherId),
                'regex:/^[A-Z0-9]+$/', // Solo mayúsculas y números
            ],

            'specialty' => [
                'nullable',
                'string',
                'max:100',
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
            'code.unique' => 'Este código ya está en uso.',
            'code.regex' => 'El código solo puede contener letras mayúsculas y números.',
            'code.max' => 'El código no puede tener más de 50 caracteres.',
            'specialty.max' => 'La especialidad no puede tener más de 100 caracteres.',
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
            'code' => 'código',
            'specialty' => 'especialidad',
        ];
    }
}