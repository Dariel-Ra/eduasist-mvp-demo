<?php

namespace App\Models;

use App\Enums\ScheduleDay;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CourseSection extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course_sections';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'teacher_id',
        'section',
        'classroom',
        'max_students',
        'schedule_days',
        'start_time',
        'end_time',
        'active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'max_students' => 'integer',
            'active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /** 
    * Accessor/Mutator for schedule_days 
    * Returns array of strings (enum values) 
    */
    protected function scheduleDays(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value 
                ? explode(',', $value) 
                : [],
            set: fn (array|string|null $value) => match(true) {
                is_array($value) => implode(',', array_filter($value)),
                is_string($value) => $value,
                default => null,
            }
        );
    }

    /**
     * Gets schedule_days as an array of Enums
     * 
     * @return ScheduleDay[]
     */
    public function getScheduleDayEnums(): array
    {
        return ScheduleDay::fromArray($this->schedule_days);
    }

    /**
     * Set schedule_days from an array of Enums
     * 
     * @param ScheduleDay[] $days
     */
    public function setScheduleDayEnums(array $days): void
    {
        $this->schedule_days = ScheduleDay::toValues($days);
    }

    /**
     * Accessor for start_time
     */
    protected function startTime(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value 
                ? \Carbon\Carbon::createFromTimeString($value)
                : null,
        );
    }

    /**
     * Accessor for end_time
     */
    protected function endTime(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value 
                ? \Carbon\Carbon::createFromTimeString($value)
                : null,
        );
    }

    // ==================== RELATIONS ====================

    /**
     * Get the course that owns the section.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the teacher that teaches the section.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the students enrolled in the section.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 
        'course_section_student', 
        'course_section_id', 
        'student_id')
            ->using(CourseSectionStudent::class)
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Get the attendances for the section.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class, 'course_section_id');
    }

    // ==================== SCOPES ====================
    
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * esp. Scope para filtrar por día usando el Enum
     * eng. Scope to filter by day using the Enum
     */
    public function scopeWithScheduleOn($query, ScheduleDay|string $day)
    {
        $dayValue = $day instanceof ScheduleDay ? $day->value : $day;
        return $query->whereRaw("FIND_IN_SET(?, schedule_days) > 0", [$dayValue]);
    }

    // ==================== AUXILIARY METHODS ====================

    /**
     * esp. Verifica si la sección tiene clases en un día específico
     * eng. Check if the section has classes on a specific day
     */
    public function hasClassOn(ScheduleDay|string $day): bool
    {
        $dayValue = $day instanceof ScheduleDay ? $day->value : $day;
        return in_array($dayValue, $this->schedule_days, true);
    }

    /**
     * esp. Obtiene los días formateados en español
     * eng. Get the days formatted in Spanish
     */
    public function getFormattedScheduleDays(): array
    {
        return collect($this->getScheduleDayEnums())
            ->map(fn(ScheduleDay $day) => $day->label())
            ->toArray();
    }

    /**
     * esp. Obtiene los días formateados en versión corta
     * eng. Get the days formatted in short version
     */
    public function getShortScheduleDays(): array
    {
        return collect($this->getScheduleDayEnums())
            ->map(fn(ScheduleDay $day) => $day->shortLabel())
            ->toArray();
    }

    /**
     * esp. Obtiene el string completo del horario
     * eng. Get the full schedule string
     */
    public function getScheduleString(): string
    {
        $days = implode(', ', $this->getFormattedScheduleDays());
        
        return sprintf(
            '%s de %s a %s',
            $days ?: 'Sin días asignados',
            $this->start_time?->format('H:i') ?? '--:--',
            $this->end_time?->format('H:i') ?? '--:--'
        );
    }

    /**
     * esp. Obtiene el string corto del horario
     * eng. Get the short schedule string
     */
    public function getShortScheduleString(): string
    {
        $days = implode('-', $this->getShortScheduleDays());
        
        return sprintf(
            '%s %s-%s',
            $days,
            $this->start_time?->format('H:i') ?? '--',
            $this->end_time?->format('H:i') ?? '--'
        );
    }

    /**
     * esp. Verifica si la clase está en sesión ahora
     * eng. Check if the class is in session now
     */
    public function isInSession(): bool
    {
        $now = now();
        $currentDay = ScheduleDay::fromCarbonDay($now->englishDayOfWeek);

        if (!$currentDay || !$this->hasClassOn($currentDay)) {
            return false;
        }

        if (!$this->start_time || !$this->end_time) {
            return false;
        }

        $currentTime = $now->format('H:i:s');
        $startTime = $this->getRawOriginal('start_time');
        $endTime = $this->getRawOriginal('end_time');

        return $currentTime >= $startTime && $currentTime <= $endTime;
    }

    /**
     * esp. Obtiene la fecha y hora de la próxima clase
     * eng. Get the date and time of the next class
     */
    public function getNextClassDateTime(): ?\Illuminate\Support\Carbon
    {
        if (empty($this->schedule_days) || !$this->start_time) {
            return null;
        }

        $now = now();
        $currentDay = ScheduleDay::fromCarbonDay($now->englishDayOfWeek);
        $startTime = $this->getRawOriginal('start_time');

        // esp. Verificar si hay clase hoy que aún no ha comenzado
        // eng. Check if there is a class today that hasn't started yet
        if ($currentDay && 
            $this->hasClassOn($currentDay) && 
            $now->format('H:i:s') < $startTime) {
            return $now->copy()->setTimeFromTimeString($startTime);
        }

        // esp. Buscar el próximo día programado
        // eng. Find the next scheduled day
        $scheduledDays = collect($this->getScheduleDayEnums())
            ->map(fn(ScheduleDay $day) => $day->dayNumber())
            ->sort()
            ->values();

        $currentDayNumber = $now->dayOfWeekIso;
        
        // esp. Buscar el siguiente día en la misma semana
        // eng. Find the next day in the same week
        $nextDay = $scheduledDays->first(fn($day) => $day > $currentDayNumber);
        
        if ($nextDay) {
            return $now->copy()->next($nextDay)->setTimeFromTimeString($startTime);
        }

        // esp. Si no hay más días esta semana, ir al primer día de la próxima semana
        // eng. If no more days this week, go to the first day of next week
        if ($scheduledDays->isNotEmpty()) {
            return $now->copy()->next($scheduledDays->first())->setTimeFromTimeString($startTime);
        }

        return null;
    }

    public function getEnrolledCount(): int
    {
        return $this->students()->count();
    }

    public function hasAvailableSeats(): bool
    {
        if (!$this->max_students) {
            return true;
        }

        return $this->getEnrolledCount() < $this->max_students;
    }

    public function getAvailableSeats(): ?int
    {
        if (!$this->max_students) {
            return null;
        }

        return max(0, $this->max_students - $this->getEnrolledCount());
    }

    public function isFull(): bool
    {
        return !$this->hasAvailableSeats();
    }
}