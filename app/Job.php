<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    // use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dbo.PR_STP_BKJOB';
    protected $primaryKey = 'JobCode';

    /**
     * Scope a query to only include loccode and SubCatID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJob($query)
    {
        return $query->where([
        	['loccode', '=', 'KL01'],
            ['SubCatID', '=', 'TMP']
        ]);
    }

}