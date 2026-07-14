<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LdHistory extends Model
{
    protected $fillable = [
        'project_id',
        'month',
        'ld_accomplished',
        'ld_unworked',
        'ld_per_day',
        'days_overdue',
        'ld_amount',
        'remarks',
        'updated_by',
    ];

    protected $casts = [
        'month' => 'date',
        'ld_accomplished' => 'float',
        'ld_unworked' => 'float',
        'ld_per_day' => 'float',
        'ld_amount' => 'float',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}