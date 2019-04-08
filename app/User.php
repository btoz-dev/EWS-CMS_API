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
        /**
         * belongsToMany()
         * @param Model to relation
         * @param Table name
         * @param FK pivot parent model
         * @param FK related
        */
        return $this->belongsToMany(Role::class, 'dbo.EWS_PEKERJA', 'codePekerja', 'idRole', 'codePekerja', 'id');
    }

    public function hasRole($roles): bool
    {
        $this->have_role = $this->getUserRole();
        // echo json_encode($this->have_role);
        // // echo var_dump($this->getUserRole());
        // exit();
        // Check if the user is a root account
        if($this->have_role->namaRole == 'Super Admin') {
            return true;
        }
        if(is_array($roles)){
            foreach($roles as $need_role){
                if($this->checkIfUserHasRole($need_role)) {
                    return true;
                }
            }
        } else{
            return $this->checkIfUserHasRole($roles);
        }
        return false;
        // return (bool) $this->roles()->where('idRole', $role)->first();
    }

    private function getUserRole()
    {
        return $this->roles()->first();
    }

    private function checkIfUserHasRole($need_role)
    {
        return (strtolower($need_role)==strtolower($this->have_role->namaRole)) ? true : false;
    }

    public function hasAnyRole($roles): bool
    {
        return (bool) $this->roles()->whereIn('idRole', $roles)->first();
    }

    public function pekerja()
    {
        # code...
        return $this->hasOne('App\Pekerja', 'codePekerja', 'codePekerja');
    }
}
