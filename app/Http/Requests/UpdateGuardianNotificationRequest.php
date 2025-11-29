<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuardianNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Implementar política de autorización cuando se configuren los roles
        // return auth()->user()->can('update', $this->route('guardian_notification'));
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
            'attendance_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:student_attendances,id',
            ],
 
            'guardian_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:guardians,id',
            ],
 
            'type' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['late', 'absent', 'excused']),
            ],
 
            'method' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['email', 'sms', 'whatsapp']),
            ],
 
            'message' => [
                'nullable',
                'string',
                'max:5000',
            ],
 
            'status' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['pending', 'sent', 'failed']),
            ],
 
            'sent_at' => [
                'nullable',
                'date',
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
            'attendance_id.required' => 'El registro de asistencia es obligatorio.',
            'attendance_id.exists' => 'El registro de asistencia seleccionado no existe.',
            'guardian_id.required' => 'El tutor es obligatorio.',
            'guardian_id.exists' => 'El tutor seleccionado no existe.',
            'type.required' => 'El tipo de notificación es obligatorio.',
            'type.in' => 'El tipo debe ser: tardanza, ausencia o justificación.',
            'method.required' => 'El método de envío es obligatorio.',
            'method.in' => 'El método debe ser: email, sms o whatsapp.',
            'message.max' => 'El mensaje no puede tener más de 5000 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado debe ser: pendiente, enviado o fallido.',
            'sent_at.date' => 'La fecha de envío debe ser válida.',
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
            'attendance_id' => 'registro de asistencia',
            'guardian_id' => 'tutor',
            'type' => 'tipo',
            'method' => 'método',
            'message' => 'mensaje',
            'status' => 'estado',
            'sent_at' => 'fecha de envío',
        ];
    }
 
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Si el estado cambia a 'sent' y no hay fecha de envío, establecerla
        if ($this->has('status') && $this->status === 'sent' && !$this->has('sent_at')) {
            $this->merge([
                'sent_at' => now(),
            ]);
        }
 
        // Si el estado cambia a 'pending' o 'failed', limpiar fecha de envío
        if ($this->has('status') && in_array($this->status, ['pending', 'failed'])) {
            $this->merge([
                'sent_at' => null,
            ]);
        }
    }
 
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $notification = $this->route('guardian_notification');
 
            // Validar que el tutor esté asociado al estudiante del registro de asistencia
            $attendanceId = $this->attendance_id ?? $notification?->attendance_id;
            $guardianId = $this->guardian_id ?? $notification?->guardian_id;
 
            if ($attendanceId && $guardianId) {
                $attendance = \App\Models\StudentAttendance::find($attendanceId);
 
                if ($attendance) {
                    $isGuardianOfStudent = \App\Models\GuardianStudent::where('guardian_id', $guardianId)
                        ->where('student_id', $attendance->student_id)
                        ->exists();
 
                    if (!$isGuardianOfStudent) {
                        $validator->errors()->add(
                            'guardian_id',
                            'El tutor seleccionado no está asociado al estudiante del registro de asistencia.'
                        );
                    }
                }
            }
 
            // Validar método según la información del tutor si se está cambiando
            if ($this->has('method') && $guardianId) {
                $guardian = \App\Models\Guardian::find($guardianId);
 
                if ($guardian) {
                    $hasMethod = match($this->method) {
                        'email' => !empty($guardian->personal_email) || !empty($guardian->user->email),
                        'sms' => !empty($guardian->phone_number),
                        'whatsapp' => !empty($guardian->whatsapp_number),
                        default => false,
                    };
 
                    if (!$hasMethod) {
                        $validator->errors()->add(
                            'method',
                            sprintf(
                                'El tutor no tiene configurado %s para recibir notificaciones.',
                                match($this->method) {
                                    'email' => 'un correo electrónico',
                                    'sms' => 'un número de teléfono',
                                    'whatsapp' => 'un número de WhatsApp',
                                    default => 'este método',
                                }
                            )
                        );
                    }
                }
            }
        });
    }
}
