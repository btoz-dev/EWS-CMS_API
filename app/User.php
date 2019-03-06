<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
    'name', 'email', 'password', 'username',
    ];


    /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
    protected $hidden = [
    'password', 'remember_token',
    ];

    public function findForPassport($identifier) {
        return $this->orWhere('email', $identifier)->orWhere('username', $identifier)->first();
    }
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    // protected $table = 'dbo.EWS_USER';

    /**
     * The primaryKey associated with the table.
     *
     * @var string
     */
    // protected $primaryKey = 'codePekerja';
    // public $timestamps   = false;

    public function roles() 
    {
        return $this->belongsToMany(Role::class, 'dbo.EWS_PEKERJA', 'codePekerja', 'idRole', 'codePekerja', 'id');
    }

     public function checkRoles($roles) 
    {
        if ( ! is_array($roles)) {
            $roles = [$roles];    
        }

        if ( ! $this->hasAnyRole($roles)) {
            // auth()->logout();
            // abort(404);
            return FALSE;
        }else
        {
            return TRUE;
        }
    }

    public function hasAnyRole($roles): bool
    {
        return (bool) $this->roles()->whereIn('idRole', $roles)->first();
    }

    public function hasRole($role): bool
    {
        return (bool) $this->roles()->where('idRole', $role)->first();
    }

    public function pekerja()
    {
        # code...
        return $this->hasOne('App\Pekerja', 'codePekerja', 'codePekerja');
    }
}
