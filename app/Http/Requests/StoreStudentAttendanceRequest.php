<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Implementar política de autorización cuando se configuren los roles
        // return auth()->user()->can('create', StudentAttendance::class);
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
                // Validar que el estudiante esté inscrito en la sección
                Rule::exists('course_section_student')
                    ->where('course_section_id', $this->course_section_id)
                    ->where('student_id', $this->student_id),
            ],
 
            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id',
            ],
 
            'date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
 
            'check_in_time' => [
                'nullable',
                'date_format:H:i:s',
            ],
 
            'status' => [
                'required',
                'string',
                Rule::in(['present', 'late', 'absent', 'excused']),
            ],
 
            'notes' => [
                'nullable',
                'string',
                'max:1000',
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
            'student_id.exists' => 'El estudiante seleccionado no existe o no está inscrito en esta sección.',
            'teacher_id.required' => 'El profesor es obligatorio.',
            'teacher_id.exists' => 'El profesor seleccionado no existe.',
            'date.required' => 'La fecha es obligatoria.',
            'date.date' => 'La fecha debe ser válida.',
            'date.before_or_equal' => 'La fecha no puede ser futura.',
            'check_in_time.date_format' => 'La hora de entrada debe tener el formato HH:MM:SS.',
            'status.required' => 'El estado de asistencia es obligatorio.',
            'status.in' => 'El estado debe ser: presente, tardanza, ausente o justificado.',
            'notes.max' => 'Las notas no pueden tener más de 1000 caracteres.',
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
            'teacher_id' => 'profesor',
            'date' => 'fecha',
            'check_in_time' => 'hora de entrada',
            'status' => 'estado',
            'notes' => 'notas',
        ];
    }
 
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Establecer fecha actual si no se proporciona
        if (!$this->has('date')) {
            $this->merge([
                'date' => now()->toDateString(),
            ]);
        }
 
        // Establecer el profesor autenticado si existe
        if (!$this->has('teacher_id') && auth()->user()?->teacher) {
            $this->merge([
                'teacher_id' => auth()->user()->teacher->id,
            ]);
        }
 
        // Establecer hora de entrada actual si el estado es presente o late
        if (!$this->has('check_in_time') && in_array($this->status, ['present', 'late'])) {
            $this->merge([
                'check_in_time' => now()->format('H:i:s'),
            ]);
        }
    }
 
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validar que no exista ya un registro de asistencia para esta combinación
            if ($this->course_section_id && $this->student_id && $this->date) {
                $exists = \App\Models\StudentAttendance::where('course_section_id', $this->course_section_id)
                    ->where('student_id', $this->student_id)
                    ->where('date', $this->date)
                    ->exists();
 
                if ($exists) {
                    $validator->errors()->add(
                        'student_id',
                        'Ya existe un registro de asistencia para este estudiante en esta fecha.'
                    );
                }
            }
 
            // Validar que la sección esté activa
            if ($this->course_section_id) {
                $courseSection = \App\Models\CourseSection::find($this->course_section_id);
 
                if ($courseSection && !$courseSection->active) {
                    $validator->errors()->add(
                        'course_section_id',
                        'No se puede registrar asistencia en una sección inactiva.'
                    );
                }
            }
        });
    }
}
