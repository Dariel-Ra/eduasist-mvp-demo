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
        return $this->belongsToMany(Student::class, 'course_section_student', 'course_section_id', 'student_id')
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
}
