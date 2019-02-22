<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
	protected $table = 'dbo.EWS_ROLE_USER';
    public function users() 
    {
    	
        return $this->belongsToMany(User::class);
    }
}
