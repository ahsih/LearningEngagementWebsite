<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    //This module belong to many user class
    public function users(){
        return $this->belongsToMany(User::class);
    }

    /**
     * Has one first choice
     */
    public function firstChoice(){
       return $this->hasOne(FirstChoiceUserModule::class);
    }
}