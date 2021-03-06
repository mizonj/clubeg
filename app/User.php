<?php

namespace App;

use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use Notifiable;
    use HasRolesAndAbilities;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'handicap', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    // Return user roles in an array
    public function listRoles()
    {
        return $this->roles->pluck('name')->toArray();
    }

    // Delete Roles for an user
    public function deleteRoles() {
        $roles = $this->listRoles();
        foreach ($roles as $role) {
            $this->retract($role);
        }
    }

    // Assign roles to an user
    public function assignRoles($roles) {
        for ($i = 0; $i < count($roles); $i++ ) {
            $this->assign($roles[$i]);
        }
    }

    public function golfclubs()
    {
        return $this->belongsToMany('App\Golfclub')
          ->withTimestamps();
    }

    public function tournaments()
    {
        return $this->belongsToMany('App\Tournament')
          ->withTimestamps();
    }

    public function groups()
    {
        return $this->belongsToMany('App\Group')
          ->withTimestamps();
    }

    public function teams()
    {
        return $this->belongsToMany('App\Team')
          ->withTimestamps();
    }

    public function livescores()
    {
        return $this->hasMany('App\Livescore', 'user_id');
    }
}
