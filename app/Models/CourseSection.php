<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
            'schedule_days' => 'array', // SET se puede manejar como array
            'start_time' => 'datetime:H:i:s', // o simplemente 'string' si prefieres
            'end_time' => 'datetime:H:i:s',
            'created_at' => 'datetime',
        ];
    }

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
        return $this->hasMany(StudentAttendance::class, 
        'course_section_id');
    }


    /**
     * Available days for schedule
     *
     * @var array
     */
    const SCHEDULE_DAYS = [
        'monday' => 'Lunes',
        'tuesday' => 'Martes',
        'wednesday' => 'Miércoles',
        'thursday' => 'Jueves',
        'friday' => 'Viernes',
    ];


       /**
     * Accessors & Mutators
     */

    /**
     * Get schedule days as array.
     *
     * @param  string|null  $value
     * @return array
     */
    public function getScheduleDaysAttribute($value)
    {
        // MySQL SET returns comma-separated values
        return $value !== null && $value !== '' ? explode(',', $value) : [];
    }

    /**
     * Set schedule days from array.
     *
     * @param  array|string  $value
     * @return void
     */
    public function setScheduleDaysAttribute($value)
    {
        $this->attributes['schedule_days'] = is_array($value) 
        ? implode(',', array_filter($value)) // Elimina valores vacíos
        : $value ?? ''; // Maneja null explícitamente
    }

       
    /**
     * Helper Methods
     */

    /**
     * Check if the section has classes on a specific day.
     *
     * @param  string  $day
     * @return bool
     */
    public function hasClassOn(string $day): bool
    {
        return in_array($day, $this->schedule_days);
    }

    /**
     * Get formatted schedule days in Spanish.
     *
     * @return array
     */
    public function getFormattedScheduleDays(): array
    {
        $days = [];
        foreach ($this->schedule_days as $day) {
            if (isset(self::SCHEDULE_DAYS[$day])) {
                $days[] = self::SCHEDULE_DAYS[$day];
            }
        }
        return $days;
    }

    /**
     * Get formatted schedule string.
     *
     * @return string
     */
    public function getScheduleString(): string
    {
        $days = implode(', ', $this->getFormattedScheduleDays());
        return sprintf('%s de %s a %s', 
            $days, 
            $this->start_time ? $this->start_time->format('H:i') : '',
            $this->end_time ? $this->end_time->format('H:i') : ''
        );
    }

    /**
     * Check if a class is happening now.
     *
     * @return bool
     */
    public function isInSession(): bool
    {
        $now = now();
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');

        // Check if today is a scheduled day
        if (!$this->hasClassOn($currentDay)) {
            return false;
        }

        // Check if current time is within class hours
        return $currentTime >= $this->start_time && 
               $currentTime <= $this->end_time;
    }

    /**
     * Get the next class date and time.
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function getNextClassDateTime()
    {
        if (empty($this->schedule_days)) {
            return null;
        }

        $now = now();
        $currentDayName = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');

        // Map day names to Carbon day constants
        $dayMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
        ];

        // Check if there's a class today that hasn't started yet
        if ($this->hasClassOn($currentDayName) && $currentTime < $this->start_time) {
            return $now->copy()->setTimeFromTimeString($this->start_time);
        }

        // Find the next scheduled day
        $scheduledDays = array_map(function($day) use ($dayMap) {
            return $dayMap[$day];
        }, $this->schedule_days);

        sort($scheduledDays);

        $currentDayNumber = $now->dayOfWeekIso;
        
        // Find next day after today
        foreach ($scheduledDays as $day) {
            if ($day > $currentDayNumber) {
                return $now->copy()->next($day)->setTimeFromTimeString($this->start_time);
            }
        }
        // If no day found this week, get the first day of next week
        if (!empty($scheduledDays)) {
            return $now->copy()->next($scheduledDays[0])->setTimeFromTimeString($this->start_time);
        }

        return null;
    }
}
