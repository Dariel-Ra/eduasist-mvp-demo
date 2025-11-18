<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * Get the parents associated with the student.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(
            ParentsModel::class,
            'parent_student',
            'student_id',
            'parent_id')
        ->using(ParentStudent::class)
        ->withPivot('relationship', 'is_primary')
        ->withTimestamps();
    }

    /**
     * Get the full name of the student.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
