<?php

namespace Skywalker\Footprints;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    /**
     * The name of the database table.
     *
     * @var string
     */
    protected $table = 'visits';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Override constructor to set the table name @ time of instantiation.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('footprints.table_name'));

        if (config('footprints.connection_name')) {
            $this->setConnection(config('footprints.connection_name'));
        }
    }

    /**
     * Get the account that owns the visit.
     */
    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        $model = config('footprints.model');

        return $this->belongsTo($model, config('footprints.column_name'));
    }

    /**
     * Scope a query to only include previous visits.
     */
    public function scopePreviousVisits($query, $footprint)
    {
        return $query->where('footprint', $footprint);
    }

    /**
     * Scope a query to only include previous visits that have been unassigned.
     */
    public function scopeUnassignedPreviousVisits($query, $footprint)
    {
        return $query->whereNull(config('footprints.column_name'))->where('footprint', $footprint);
    }

    /**
     * Scope a query to only include unassigned visits older than $days days.
     */
    public function scopePrunable($query, $days)
    {
        return $query->whereNull(config('footprints.column_name'))->where('created_at', '<=', today()->subDays($days));
    }
}


