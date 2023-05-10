<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property bool $active
 * @property string $name
 * @property int $project_id
 * @property int $count
 * @property int $item_template_id
 * @property int $event_id
 * @property int $event_fields
 */
class Achievements extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'active',
        'name',
        'project_id',
        'count',
        'item_template_id',
        'event_id',
        'event_fields',
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
        'active' => 'bool',
        'event_fields' => 'json'
    ];

    protected $attributes = [
        'event_fields' => '{}',
    ];

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class,
        );
    }

    /**
     * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(
            Event::class,
        );
    }

    /**
     * @return BelongsTo
     */
    public function itemTemplate(): BelongsTo
    {
        return $this->belongsTo(
            ItemTemplate::class,
        );
    }
}
