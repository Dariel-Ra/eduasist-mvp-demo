<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseSectionStudentRequest extends FormRequest
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
            'course_section_id' => [
                'required',
                'integer',
                'exists:course_sections,id',
            ],
 
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
                // Validar que el estudiante no esté ya inscrito en esta sección
                Rule::unique('course_section_student')
                    ->where('course_section_id', $this->course_section_id)
                    ->where('student_id', $this->student_id),
            ],
 
            'status' => [
                'nullable',
                'string',
                Rule::in(['active', 'dropped']),
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
            'course_section_id.required' => 'La sección del curso es obligatoria.',
            'course_section_id.exists' => 'La sección del curso seleccionada no existe.',
            'student_id.required' => 'El estudiante es obligatorio.',
            'student_id.exists' => 'El estudiante seleccionado no existe.',
            'student_id.unique' => 'El estudiante ya está inscrito en esta sección.',
            'status.in' => 'El estado debe ser "active" o "dropped".',
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
            'course_section_id' => 'sección del curso',
            'student_id' => 'estudiante',
            'status' => 'estado',
        ];
    }
 
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Establecer valores por defecto
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'active',
            ]);
        }
    }
 
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que la sección tenga espacio disponible
            if ($this->course_section_id) {
                $courseSection = \App\Models\CourseSection::find($this->course_section_id);
 
                if ($courseSection && !$courseSection->hasAvailableSeats()) {
                    $validator->errors()->add(
                        'course_section_id',
                        'La sección no tiene cupos disponibles.'
                    );
                }
 
                // Validar que la sección esté activa
                if ($courseSection && !$courseSection->active) {
                    $validator->errors()->add(
                        'course_section_id',
                        'No se puede inscribir estudiantes en una sección inactiva.'
                    );
                }
            }
 
            // Validar que el estudiante esté activo
            if ($this->student_id) {
                $student = \App\Models\Student::find($this->student_id);
 
                if ($student && !$student->active) {
                    $validator->errors()->add(
                        'student_id',
                        'No se puede inscribir un estudiante inactivo.'
                    );
                }
            }
        });
    }
}
