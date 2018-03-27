<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class ActiveLesson extends Model
{

    protected $primaryKey = 'lesson_id';

    /**
     * This belong to the lesson table
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lesson(){
        return $this->belongsTo(Lesson::class);
    }

    /**
     * This belong to the module class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(){
        return $this->belongsTo(Module::class);
    }
}
