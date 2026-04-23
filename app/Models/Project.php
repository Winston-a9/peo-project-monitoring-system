<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'remaining_balance',
        // Overview
        'contract_id',
        'in_charge',
        'division',         
        'project_title',
        'location',
        'contractor',
        'original_contract_amount',
        'date_started',
        'contract_days',
        'original_contract_expiry',
        'revised_contract_expiry',
        'status',
        'completed_at',
        'performance_bond_date',
        // Performance
        'as_planned',
        'work_done',
        'slippage',
        'progress_updated_at',
        'ld_accomplished',
        'ld_unworked',
        'ld_per_day',
        'total_ld',
        'ld_days_overdue',
        'ld_status',
        'ld_start_date',
        'ld_end_date',
        // Extension
        'time_extension',
        'extension_days',
        'cost_involved',
        'suspension_days',
        'variation_order',
        'vo_days',
        'vo_cost',
        'date_requested',
        // Billing updates
        'advance_billing_pct',
        'advance_billing_amount',
        'retention_pct',
        'retention_amount',
        'billing_amounts',
        'billing_dates',
        'total_amount_billed',
        // Admin
        'remarks_recommendation',
        'issuances',
        'documents_pressed',
    ];

    protected function casts(): array
    {
        return [
            'date_started'             => 'date',
            'original_contract_expiry' => 'date',
            'revised_contract_expiry'  => 'date',
            'completed_at'             => 'date',
            'performance_bond_date'    => 'date',
            'progress_updated_at'      => 'datetime',
            'original_contract_amount' => 'decimal:2',
            'as_planned'               => 'decimal:3',
            'work_done'                => 'decimal:3',
            'slippage'                 => 'decimal:3',
            'remaining_balance'        => 'decimal:2',
            'total_amount_billed'      => 'decimal:2',
            'ld_accomplished'          => 'decimal:3',
            'ld_unworked'              => 'decimal:2',
            'ld_per_day'               => 'decimal:2',
            'total_ld'                 => 'decimal:2',
            'advance_billing_pct'      => 'decimal:2',
            'advance_billing_amount'   => 'decimal:2',
            'retention_pct'            => 'decimal:2',
            'retention_amount'         => 'decimal:2',
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
            'ld_start_date'            => 'date',
            'ld_end_date'              => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::updated(function (Project $project) {
            $dirty    = $project->getDirty();

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

            if (! empty($changes)) {
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
        return $this->hasMany(\App\Models\ProjectLog::class);
    }
}