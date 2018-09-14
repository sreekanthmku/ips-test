<?php

namespace App;

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
     * Return completed modules
     *
     */
    public function completed_modules()
    {
        return $this->belongsToMany('App\Module', 'user_completed_modules');
    }

    
    /**
     * Return completed modules in the order which it is to be completed
     *
     */
    public function completed_modules_by_order($order)
    {
        return $this->belongsToMany('App\Module', 'user_completed_modules')->orderByRaw('FIELD(modules.name,"'.$order.'")')->groupBy(['modules.course_key', 'modules.name']);
    }
}
