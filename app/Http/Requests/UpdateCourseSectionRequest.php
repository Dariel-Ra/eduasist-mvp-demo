<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\ScheduleDay;

class UpdateCourseSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Aquí puedes agregar lógica de autorización
        // Por ejemplo: return auth()->user()->can('update', $this->courseSection);
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
                'required',
                'integer',
                'exists:courses,id',
            ],

            'teacher_id' => [
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
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
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
            'section.max' => 'La sección no puede tener más de 50 caracteres.',
            'classroom.max' => 'El aula no puede tener más de 50 caracteres.',
            'max_students.min' => 'El número máximo de estudiantes debe ser al menos 1.',
            'max_students.max' => 'El número máximo de estudiantes no puede exceder 100.',
            'schedule_days.required' => 'Debe seleccionar al menos un día de clases.',
            'schedule_days.array' => 'Los días de clases deben ser un arreglo.',
            'schedule_days.min' => 'Debe seleccionar al menos un día de clases.',
            'schedule_days.*.in' => 'Día de clase inválido. Debe ser lunes, martes, miércoles, jueves o viernes.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'start_time.date_format' => 'La hora de inicio debe tener el formato HH:MM (ej: 08:00).',
            'end_time.required' => 'La hora de fin es obligatoria.',
            'end_time.date_format' => 'La hora de fin debe tener el formato HH:MM (ej: 10:00).',
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
            'max_students' => 'número máximo de estudiantes',
            'schedule_days' => 'días de clases',
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
        // Agregar :00 a las horas si no tienen segundos
        if ($this->has('start_time') && strlen($this->start_time) === 5) {
            $this->merge([
                'start_time' => $this->start_time . ':00',
            ]);
        }

        if ($this->has('end_time') && strlen($this->end_time) === 5) {
            $this->merge([
                'end_time' => $this->end_time . ':00',
            ]);
        }
    }
}
