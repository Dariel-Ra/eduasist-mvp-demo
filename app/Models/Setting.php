<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

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
        'late_threshold_minutes',
        'auto_notify_parents',
        'notification_delay_minutes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'late_threshold_minutes' => 'integer',
            'auto_notify_parents' => 'boolean',
            'notification_delay_minutes' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the singleton setting instance.
     */
    public static function getInstance(): self
    {
        return self::firstOrCreate([], [
            'late_threshold_minutes' => 15,
            'auto_notify_parents' => true,
            'notification_delay_minutes' => 30,
        ]);
    }
}
