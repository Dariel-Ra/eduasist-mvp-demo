<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'students';

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
        'first_name',
        'last_name',
        'enrollment_code',
        'date_of_birth',
        'grade_level',
        'section',
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
            'date_of_birth' => 'date',
            'active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the guardians associated with the student.
     */
    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student', 'student_id', 'guardian_id')
            ->using(GuardianStudent::class)
            ->withPivot('relationship', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Get the sections the student is enrolled in.
     */
    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(CourseSection::class, 'course_section_student', 'student_id', 'course_section_id')
            ->using(CourseSectionStudent::class)
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Get the attendances for the student.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class, 'student_id');
    }

    /**
     * Get the full name of the student.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
