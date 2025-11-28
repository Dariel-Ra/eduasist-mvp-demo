<?php

namespace App\Http\Requests;

use App\Enums\ScheduleDay;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseSectionRequest extends FormRequest
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
            'course_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:courses,id',
            ],
 
            'teacher_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:teachers,id',
            ],
 
            'section' => [
                'nullable',
                'string',
                'max:50',
            ],
 
            'classroom' => [
                'nullable',
                'string',
                'max:50',
            ],
 
            'max_students' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
 
            'schedule_days' => [
                'sometimes',
                'required',
                'array',
                'min:1',
            ],
 
            'schedule_days.*' => [
                'required',
                'string',
                Rule::in(ScheduleDay::values()),
            ],
 
            'start_time' => [
                'sometimes',
                'required',
                'date_format:H:i',
            ],
 
            'end_time' => [
                'sometimes',
                'required',
                'date_format:H:i',
                'after:start_time',
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
            'course_id.required' => 'El curso es obligatorio.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            'teacher_id.required' => 'El profesor es obligatorio.',
            'teacher_id.exists' => 'El profesor seleccionado no existe.',
 
            'section.max' => 'El nombre de la sección no puede tener más de 50 caracteres.',
            'classroom.max' => 'El nombre del aula no puede tener más de 50 caracteres.',
            'max_students.min' => 'El número máximo de estudiantes debe ser al menos 1.',
            'max_students.max' => 'El número máximo de estudiantes no puede ser mayor a 100.',
 
            'schedule_days.required' => 'Debe seleccionar al menos un día de la semana.',
            'schedule_days.min' => 'Debe seleccionar al menos un día de la semana.',
            'schedule_days.*.in' => 'El día seleccionado no es válido.',
 
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'start_time.date_format' => 'La hora de inicio debe tener el formato HH:MM.',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.date_format' => 'La hora de fin debe tener el formato HH:MM.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
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
            'course_id' => 'curso',
            'teacher_id' => 'profesor',
            'section' => 'sección',
            'classroom' => 'aula',
            'max_students' => 'máximo de estudiantes',
            'schedule_days' => 'días de la semana',
            'start_time' => 'hora de inicio',
            'end_time' => 'hora de fin',
            'active' => 'estado activo',
        ];
    }
 
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Asegurar que schedule_days sea un array si está presente
        if ($this->has('schedule_days') && is_string($this->schedule_days)) {
            $this->merge([
                'schedule_days' => explode(',', $this->schedule_days),
            ]);
        }
    }
}
