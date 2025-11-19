<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Aquí puedes agregar lógica de autorización
        // Por ejemplo: return auth()->user()->can('create', Teacher::class);
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
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                'unique:teachers,user_id',
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                'unique:teachers,code',
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
            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
            'user_id.unique' => 'Este usuario ya tiene un perfil de profesor.',
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
            'user_id' => 'usuario',
            'code' => 'código',
            'specialty' => 'especialidad',
        ];
    }
}
