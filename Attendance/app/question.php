<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class question extends Model
{
    /**
     * This model has many optional answer
     */
    public function optionalAnswers()
    {
        return $this->hasMany(optionalAnswers::class);
    }

    /**
     * This belong to the lessons
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lesson(){
        return $this->belongsTo(Lesson::class,'lesson_id');
    }

    /**
     * This model belong to the user
     */
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * This model has many response
     */
    public function responses(){
        return $this->hasMany(Response::class);
    }

}
