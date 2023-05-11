<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $event_id
 * @property int $user_id
 * @property int $count
 * @property string $fields
 * @property string $fields_hash
 */
class EventUser extends BaseModel
{
    use HasFactory;

    protected $table = 'event_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'count',
        'fields',
        'fields_hash',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'fields' => 'json'
    ];

    protected $attributes = [
        'fields' => '{}',
    ];

    public function user()
    {
        return $this->hasOne(
            User::class,
            'id',
            'user_id'
        );
    }

    public function event()
    {
        return $this->hasOne(
            Event::class,
            'id',
            'event_id'
        );
    }

    public function isAchievment()
    {
        return $this->event();
    }
}
