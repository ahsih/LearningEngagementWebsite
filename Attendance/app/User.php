<?php

namespace attendance;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Define the role as belogns to many users
     */
    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    /**
     * Users can belongs to many modules
     */
    public function modules(){
        return $this->belongsToMany(Module::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * Has one first choice module
     */
    public function firstChoice(){
        return $this->hasOne(FirstChoiceUserModule::class);
    }
    
    /**
     * Authorise this user if he has role
     */
    public function authorizeRoles($roles)
    {
      if (is_array($roles)) {
          return $this->hasAnyRole($roles) || 
                 abort(401, 'This action is unauthorized.');
      }
      return $this->hasRole($roles) || 
             abort(401, 'This action is unauthorized.');
    }

    /**
     * Check user has role
     */
    public function hasAnyRole($roles)
    {
      return null !== $this->roles()->whereIn("name", $roles)->first();
    }

     /**
    * Check one role
    * @param string $role
    */
    public function hasRole($role)
        {
        return null !== $this->roles()->where("name", $role)->first();
        }
    }
