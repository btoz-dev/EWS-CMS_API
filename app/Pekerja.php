<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pekerja extends Model
{
    //
	protected $table = 'dbo.EWS_PEKERJA';

	public function user()
	{
		# code...
		return $this->belongsTo('App\User', 'codePekerja', 'codePekerja');
	}
}
