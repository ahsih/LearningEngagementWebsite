<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    /**
     * This belong to the modules
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function modules(){
        return $this->belongsTo(Module::class);
    }

    /**
     * This has many questions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions(){
        return $this->hasMany(question::class);
    }

    /**
     * This has one lesson pointer
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lessonPointer(){
        return $this->hasOne(ActiveLesson::class);
    }
}
