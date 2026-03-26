<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'in_charge',
        'project_title',
        'location',
        'contractor',
        'contract_amount',
        'original_contract_amount',
        'date_started',
        'contract_days',
        'original_contract_expiry',
        'revised_contract_expiry',
        'as_planned',
        'work_done',
        'slippage',
        'progress_updated_at',
        'status',
        'completed_at',
        'remarks_recommendation',
        // Documents & Extensions
        'issuances',
        'documents_pressed',
        'time_extension',
        'extension_days',
        'cost_involved',
        'suspension_days',
        'variation_order',
        'vo_days',
        'vo_cost',
        'date_requested',
        'performance_bond_date',
        'billing_amounts',
        'billing_dates',
        'remaining_balance',
        'total_amount_billed',
        // Liquidated Damages
        'ld_accomplished',
        'ld_unworked',
        'ld_per_day',
        'total_ld',
        'ld_days_overdue',
    ];

    protected function casts(): array
    {
        return [
            'date_started'             => 'date',
            'original_contract_expiry' => 'date',
            'revised_contract_expiry'  => 'date',
            'completed_at'             => 'date',
            'progress_updated_at'      => 'datetime',
            'contract_amount'          => 'decimal:2',
            'original_contract_amount' => 'decimal:2',
            'as_planned'               => 'decimal:3',
            'work_done'                => 'decimal:3',
            'slippage'                 => 'decimal:3',
            'performance_bond_date'    => 'date',
            'remaining_balance'        => 'decimal:2',
            'total_amount_billed'      => 'decimal:2',
            'ld_accomplished'          => 'decimal:3',
            'ld_unworked'              => 'decimal:2',
            'ld_per_day'               => 'decimal:2',
            'total_ld'                 => 'decimal:2',
            'contract_days'            => 'integer',
            'suspension_days'          => 'integer',
            'time_extension'           => 'integer',
            'variation_order'          => 'integer',
            'ld_days_overdue'          => 'integer',
            'billing_amounts'          => 'array',
            'billing_dates'            => 'array',
            'issuances'                => 'array',
            'documents_pressed'        => 'array',
            'extension_days'           => 'array',      
            'cost_involved'            => 'array',
            'vo_days'                  => 'array',
            'vo_cost'                  => 'array',
            'date_requested'           => 'array',

        ];
    }

    protected static function booted(): void
    {
        static::updated(function (Project $project) {
            $dirty    = $project->getDirty();

            // ── Auto-stamp when progress fields change ──
            if (array_key_exists('as_planned', $dirty) || array_key_exists('work_done', $dirty)) {
                \Illuminate\Support\Facades\DB::table('projects')
                    ->where('id', $project->id)
                    ->update(['progress_updated_at' => now()]);
            }
            $original = collect($dirty)->mapWithKeys(fn($v, $k) => [$k => $project->getOriginal($k)])->toArray();

            $skip    = ['updated_at'];
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