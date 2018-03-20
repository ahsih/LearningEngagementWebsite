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

    /**
     * This has many modules
     */
    public function responses(){
        return $this->hasMany(Response::class);
    }

    /**
     * This model has many questions
     */
    public function question(){
       return $this->hasMany(question::class);
    }
}
