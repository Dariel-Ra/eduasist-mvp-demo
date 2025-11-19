<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentAttendance extends Model
{
    use HasFactory;
 
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'student_attendances';

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
        'course_section_id',
        'student_id',
        'teacher_id',
        'date',
        'check_in_time',
        'status',
        'notes',
    ];
 
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_time' => 'datetime:H:i:s',
            'created_at' => 'datetime',
        ];
    }
 
    /**
     * Get the course section that owns the attendance.
     */
    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    /**
     * Get the student that owns the attendance.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
 
    /**
     * Get the teacher that recorded the attendance.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the notifications for the attendance.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(GuardianNotification::class, 'attendance_id');
    }
}
