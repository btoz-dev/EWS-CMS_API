<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pekerja extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'dbo.EWS_PEKERJA';

	/**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'codePekerja';

	public function user()
	{
		# code...
		return $this->belongsTo('App\User', 'codePekerja', 'codePekerja');
	}

	public function getNameCodeAttribute()
	{
		return "{$this->namaPekerja} [{$this->codePekerja}]";
	}
}
