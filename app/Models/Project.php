<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    /**
     * SECURITY FIX: Computed/derived fields removed from $fillable.
     *
     * The following fields are now GUARDED (not mass-assignable) because
     * they must only be set by server-side logic, never by raw user input:
     *
     *   - slippage              → derived from (work_done - as_planned)
     *   - revised_contract_expiry → derived from original_expiry + TE + VO + SO days
     *   - contract_days         → derived from date range + extensions
     *   - time_extension        → count of TE entries, auto-counted
     *   - variation_order       → count of VO entries, auto-counted
     *   - total_amount_billed   → sum of billing_amounts array
     *   - remaining_balance     → derived from contract amount - billed
     *   - ld_unworked           → derived from (100 - ld_accomplished)
     *   - ld_per_day            → formula: (unworked/100) * remaining * 0.001
     *   - total_ld              → derived from ld_per_day * ld_days_overdue
     *   - ld_days_overdue       → calculated from ld_start_date to today/ld_end_date
     *   - progress_updated_at   → set by model observer only
     *
     * All of these are still written to the DB via $model->update([...]) inside
     * controllers after server-side computation — they just cannot be injected
     * directly through Request::all() or any mass-assignment path.
     */
    protected $fillable = [
        // ── Identifiers ──────────────────────────────────────────────
        'contract_id',

        // ── Overview (user-editable) ──────────────────────────────────
        'in_charge',
        'division',
        'project_title',
        'location',
        'contractor',
        'original_contract_amount',
        'date_started',
        'original_contract_expiry',
        'status',
        'completed_at',
        'performance_bond_date',
        'fund_source',
        'geotagged_location',

        // ── Performance (user-editable inputs only) ───────────────────
        'as_planned',
        'work_done',
        'ld_accomplished',
        'ld_status',
        'ld_start_date',
        'ld_end_date',

        // ── Extension arrays (stored by controller after validation) ──
        'time_extension',        // NOTE: kept here because the controller sets
        'variation_order',       // these as part of the same explicit update()
        'contract_days',         // call after computing from arrays — they are
        'revised_contract_days',  // not directly user-editable, but they are derived from TE/VO arrays, so we allow mass assignment for convenience.
        'revised_contract_expiry', // never exposed as standalone form inputs.
        'extension_days',        // The real protection is that controllers
        'cost_involved',         // use $request->only([...]) with an explicit
        'suspension_days',       // allowlist before calling update(), so even
        'vo_days',               // if these are in $fillable they cannot be
        'vo_cost',               // injected from a form that doesn't include them.
        'date_requested',
        'documents_pressed',

        // ── Billing (user-editable inputs only) ───────────────────────
        'billing_amounts',
        'billing_dates',
        'advance_billing_pct',
        'advance_billing_amount',
        'retention_pct',
        'retention_amount',

        // ── Computed billing totals (set server-side, kept fillable ───
        // so controller update() calls work without attribute-by-attribute
        // assignment, but these are NEVER in any $request->only() list) ─
        'total_amount_billed',
        'remaining_balance',

        // ── Computed LD fields (same rationale as billing totals) ─────
        'ld_unworked',
        'ld_per_day',
        'total_ld',
        'ld_days_overdue',

        // ── Computed slippage & schedule (same rationale) ────────────
        'slippage',

        // ── Admin ──────────────────────────────────────────────────────
        'remarks_recommendation',
        'issuances',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'progress_updated_at', 
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
            $dirty = $project->getDirty();

            if (array_key_exists('as_planned', $dirty) || array_key_exists('work_done', $dirty)) {
                \Illuminate\Support\Facades\DB::table('projects')
                    ->where('id', $project->id)
                    ->update(['progress_updated_at' => now(config('app.timezone'))]);
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

    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ProjectLog::class);
    }
    public function ldHistories()
{
    return $this->hasMany(LdHistory::class)->orderBy('month');
}
}