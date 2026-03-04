<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'in_charge',
        'project_title',
        'location',
        'contractor',
        'date_started',
        'original_contract_expiry',
        'revised_contract_expiry',
        'as_planned',
        'work_done',
        'slippage',
        'remarks_recommendation',
        'status',
        'completed_at',
        'contract_amount',
        'issuances',
        'documents_pressed',
        "extension_days",
        'time_extension',
    ];

    protected function casts(): array
    {
        return [
        'date_started'             => 'date',
        'original_contract_expiry' => 'date',
        'revised_contract_expiry'  => 'date',
        'completed_at'             => 'date',
        'contract_amount'          => 'decimal:2',
        'as_planned'               => 'decimal:2',
        'work_done'                => 'decimal:2',
        'slippage'                 => 'decimal:2',
        'extension_days'           => 'json',
        'time_extension'           => 'integer',
        ];
    }
    protected static function booted(): void
{
    static::updated(function (Project $project) {
        $dirty = $project->getDirty();
        $original = collect($dirty)->mapWithKeys(fn($v, $k) => [$k => $project->getOriginal($k)])->toArray();

        // Skip internal timestamps
        $skip = ['updated_at'];
        $changes = [];
        foreach ($dirty as $field => $newVal) {
            if (in_array($field, $skip)) continue;
            $changes[$field] = ['from' => $original[$field] ?? null, 'to' => $newVal];
        }

        if (!empty($changes)) {
            ProjectLog::create([
                'project_id' => $project->id,
                'user_id'    => auth()->id(),
                'action'     => 'updated',
                'changes'    => $changes,
            ]);
        }
    });

    static::created(function (Project $project) {
        ProjectLog::create([
            'project_id' => $project->id,
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'changes'    => [],
        ]);
    });
    }
    public function logs()
{
    return $this->hasMany(ProjectLog::class)->latest();
}
}