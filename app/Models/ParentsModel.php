<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentsModel extends Model
{
    use HasFactory;
 
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'parentsmodel';
 
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
        'user_id',
        'personal_email',
        'phone_number',
        'whatsapp_number',
    ];
 
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the parent profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    /**
     * Get the students associated with the parent.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 
            'parent_student',
            'parent_id', 
            'student_id')
            ->using(ParentStudent::class)
            ->withPivot('relationship', 'is_primary')
            ->withTimestamps();
    }
}

