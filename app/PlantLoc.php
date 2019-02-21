<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlantLoc extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dbo.GL_SUBBLKHILL';
    protected $primaryKey = 'SubBlkHillCode';
    protected $keyType  = 'string';

    /**
     * Scope a query to only SubBlkCode of plant.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $SubBlkCode
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlock($query, $block)
    {
        return $query->where('SubBlkCode', $block);
    }

    /**
     * Scope a query to only Plot of plant.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $plot
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePlot($query, $plot)
    {
        return $query->where('Plot', $plot);
    }

    /**
     * Scope a query to only Baris of plant.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $row
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRow($query, $row)
    {
        return $query->where('Baris', $row);
    }

    /**
     * Scope a query to only NoTanam of plant.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $pokok
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePokok($query, $pokok)
    {
        return $query->where('NoTanam', $pokok);
    }
}
