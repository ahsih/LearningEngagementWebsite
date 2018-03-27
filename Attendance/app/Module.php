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
    public function lessons(){
       return $this->hasMany(Lesson::class);
    }

    /**
     * This has one lesson pointer
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lessonPointer(){
        return $this->hasOne(ActiveLesson::class);
    }

    /**
     * This has many rewards
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rewards(){
        return $this->hasMany(Reward::class);
    }
}
