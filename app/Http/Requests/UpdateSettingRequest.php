<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO: Implementar política de autorización cuando se configuren los roles
        // Solo administradores deberían poder actualizar las configuraciones
        // return auth()->user()->can('update', Setting::class);
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
            'late_threshold_minutes' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:180', // Máximo 3 horas
            ],
 
            'auto_notify_parents' => [
                'sometimes',
                'required',
                'boolean',
            ],
 
            'notification_delay_minutes' => [
                'sometimes',
                'required',
                'integer',
                'min:0',
                'max:240', // Máximo 4 horas
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
            'late_threshold_minutes.required' => 'El umbral de tardanza es obligatorio.',
            'late_threshold_minutes.integer' => 'El umbral de tardanza debe ser un número entero.',
            'late_threshold_minutes.min' => 'El umbral de tardanza debe ser al menos 1 minuto.',
            'late_threshold_minutes.max' => 'El umbral de tardanza no puede ser mayor a 180 minutos (3 horas).',
 
            'auto_notify_parents.required' => 'La configuración de notificación automática es obligatoria.',
            'auto_notify_parents.boolean' => 'La configuración de notificación automática debe ser verdadero o falso.',
 
            'notification_delay_minutes.required' => 'El retraso de notificación es obligatorio.',
            'notification_delay_minutes.integer' => 'El retraso de notificación debe ser un número entero.',
            'notification_delay_minutes.min' => 'El retraso de notificación debe ser al menos 0 minutos.',
            'notification_delay_minutes.max' => 'El retraso de notificación no puede ser mayor a 240 minutos (4 horas).',
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
            'late_threshold_minutes' => 'umbral de tardanza',
            'auto_notify_parents' => 'notificación automática a tutores',
            'notification_delay_minutes' => 'retraso de notificación',
        ];
    }
 
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validación personalizada: el retraso de notificación debería ser mayor al umbral de tardanza
            if ($this->has('late_threshold_minutes') && $this->has('notification_delay_minutes')) {
                if ($this->notification_delay_minutes < $this->late_threshold_minutes) {
                    $validator->errors()->add(
                        'notification_delay_minutes',
                        'El retraso de notificación debería ser mayor o igual al umbral de tardanza para dar tiempo a los estudiantes a llegar.'
                    );
                }
            }
 
            // Advertencia si se desactiva la notificación automática
            if ($this->has('auto_notify_parents') && !$this->auto_notify_parents) {
                // Esta es solo una advertencia informativa, no un error
                // Se podría implementar un sistema de warnings en el futuro
            }
        });
    }
 
    /**
     * Get validated data with sensible defaults.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
 
        // Asegurar que los valores numéricos sean enteros
        if (isset($validated['late_threshold_minutes'])) {
            $validated['late_threshold_minutes'] = (int) $validated['late_threshold_minutes'];
        }
 
        if (isset($validated['notification_delay_minutes'])) {
            $validated['notification_delay_minutes'] = (int) $validated['notification_delay_minutes'];
        }
 
        return $validated;
    }
}