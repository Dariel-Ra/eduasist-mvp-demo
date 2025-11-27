<?php
// app/Enums/ScheduleDay.php

namespace App\Enums;

enum ScheduleDay: string
{
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';

    /**
     * Obtiene la etiqueta en español
     */
    public function label(): string
    {
        return match($this) {
            self::MONDAY => 'Lunes',
            self::TUESDAY => 'Martes',
            self::WEDNESDAY => 'Miércoles',
            self::THURSDAY => 'Jueves',
            self::FRIDAY => 'Viernes',
        };
    }

    /**
     * Obtiene la etiqueta abreviada
     */ 
    public function shortLabel(): string
    {
        return match($this) {
            self::MONDAY => 'Lun',
            self::TUESDAY => 'Mar',
            self::WEDNESDAY => 'Mié',
            self::THURSDAY => 'Jue',
            self::FRIDAY => 'Vie',
        };
    }

    /**
     * Obtiene el número del día (ISO-8601: 1=Lunes, 7=Domingo)
     */
    public function dayNumber(): int
    {
        return match($this) {
            self::MONDAY => 1,
            self::TUESDAY => 2,
            self::WEDNESDAY => 3,
            self::THURSDAY => 4,
            self::FRIDAY => 5,
        };
    }

    /**
     * Obtiene el nombre del día en inglés para Carbon
     */
    public function carbonDayName(): string
    {
        return match($this) {
            self::MONDAY => 'Monday',
            self::TUESDAY => 'Tuesday',
            self::WEDNESDAY => 'Wednesday',
            self::THURSDAY => 'Thursday',
            self::FRIDAY => 'Friday',
        };
    }

    /**
     * Crea un ScheduleDay desde el nombre en inglés de Carbon
     */
    public static function fromCarbonDay(string $dayName): ?self
    {
        return match(strtolower($dayName)) {
            'monday' => self::MONDAY,
            'tuesday' => self::TUESDAY,
            'wednesday' => self::WEDNESDAY,
            'thursday' => self::THURSDAY,
            'friday' => self::FRIDAY,
            default => null,
        };
    }

    /**
     * Crea un ScheduleDay desde el número del día
     */
    public static function fromDayNumber(int $dayNumber): ?self
    {
        return match($dayNumber) {
            1 => self::MONDAY,
            2 => self::TUESDAY,
            3 => self::WEDNESDAY,
            4 => self::THURSDAY,
            5 => self::FRIDAY,
            default => null,
        };
    }

        /**
     * Para uso en formularios (select/checkbox)
     * Retorna: [['value' => 'monday', 'label' => 'Lunes'], ...]
     */
    public static function toOptions(): array
    {
        return collect(self::cases())
            ->map(fn($case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'shortLabel' => $case->shortLabel(),
                'dayNumber' => $case->dayNumber(),
            ])
            ->values()
            ->toArray();
    }

    /**
     * Obtiene todos los casos como array [value => label]
     */
    public static function toArray(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    /**
     * Obtiene todos los casos como array [value => short label]
     */
    public static function toShortArray(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->shortLabel()])
            ->toArray();
    }

    /**
     * Obtiene todos los valores como array simple
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtiene todos los labels como array simple
     */
    public static function labels(): array
    {
        return array_map(fn($case) => $case->label(), self::cases());
    }

    /**
     * Verifica si un valor es válido
     */
    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }

    /**
     * Obtiene múltiples casos desde un array de valores
     */
    public static function fromArray(array $values): array
    {
        return collect($values)
            ->map(fn($value) => self::tryFrom($value))
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Convierte múltiples casos a array de valores
     */
    public static function toValues(array $cases): array
    {
        return collect($cases)
            ->map(fn($case) => $case instanceof self ? $case->value : null)
            ->filter()
            ->values()
            ->toArray();
    }
}
