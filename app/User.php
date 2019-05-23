<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use DB;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
    'name', 'email', 'password', 'username', 'codePekerja', 'password_decrypt'
    ];


    /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
    protected $hidden = [
    'password', 'remember_token',
    ];
    
    public function pekerja()
    {
        return $this->hasOne('App\Pekerja', 'codePekerja', 'codePekerja');
    }
    
}
