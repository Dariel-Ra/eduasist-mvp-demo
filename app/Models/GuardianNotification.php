<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuardianNotification extends Model
{
        use HasFactory;
 
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'guardian_notifications';
 
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
        'attendance_id',
        'guardian_id',
        'type',
        'method',
        'message',
        'status',
        'sent_at',
    ];
 
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
 
    /**
     * Get the attendance that owns the notification.
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(StudentAttendance::class);
    }

    /**
     * Get the guardian that receives the notification.
     */
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }
}
