<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /**
     * This has many users
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(){
        return $this->belongsToMany(User::class);
    }

    /**
     * This has many student attendance model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentAttendance(){
        return $this->hasMany(StudentAttendance::class);
    }

    /**
     * This module has many reward achieved
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rewardAchieved(){
        return $this->hasMany(RewardAchieve::class);
    }

    /**
     * This module has many emailRequestModules class
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailRequestModules(){
        return $this->hasMany(EmailRequestModule::class);
    }

    /**
     * this has one attendance setting model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendanceSetting(){
        return $this->hasOne(AttendanceSetting::class);
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

    /**
     * This has many lesson start
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lessonStart(){
        return $this->hasMany(LessonStart::class);
    }
}
