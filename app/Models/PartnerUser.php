<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function getRoom()
    {
        $project = Project
            ::where(['id' => $this->project_id])
            ->with('roomItems')
            ->firstOrFail();

        $items = [];

        foreach ($project->roomItems as $roomItem) {
            $items[] = $roomItem->prePareforUser($this->id);
        }

        return $items;
    }
}
