<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuardianNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Implementar política de autorización cuando se configuren los roles
        // return auth()->user()->can('create', GuardianNotification::class);
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
                'required',
                'integer',
                'exists:student_attendances,id',
            ],
 
            'guardian_id' => [
                'required',
                'integer',
                'exists:guardians,id',
            ],
 
            'type' => [
                'required',
                'string',
                Rule::in(['late', 'absent', 'excused']),
            ],
 
            'method' => [
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
                'nullable',
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
        // Establecer valores por defecto
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'pending',
            ]);
        }
 
        // Si el método es email, establecer email como método por defecto
        if (!$this->has('method')) {
            $this->merge([
                'method' => 'email',
            ]);
        }
    }
 
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            // Validar que el tutor esté asociado al estudiante del registro de asistencia
            if ($this->attendance_id && $this->guardian_id) {
                $attendance = \App\Models\StudentAttendance::find($this->attendance_id);
 
                if ($attendance) {
                    $isGuardianOfStudent = \App\Models\GuardianStudent::where('guardian_id', $this->guardian_id)
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
 
            // Validar que el tipo de notificación coincida con el estado de asistencia
            if ($this->attendance_id && $this->type) {
                $attendance = \App\Models\StudentAttendance::find($this->attendance_id);
 
                if ($attendance) {
                    $validTypes = match($attendance->status) {
                        'late' => ['late'],
                        'absent' => ['absent'],
                        'excused' => ['excused'],
                        default => [],
                    };
 
                    if (!in_array($this->type, $validTypes)) {
                        $validator->errors()->add(
                            'type',
                            'El tipo de notificación no coincide con el estado de asistencia.'
                        );
                    }
                }
            }
 
            // Validar método según la información del tutor
            if ($this->guardian_id && $this->method) {
                $guardian = \App\Models\Guardian::find($this->guardian_id);
 
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
