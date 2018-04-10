<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    /**
     * This belong to the user model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * This belong to the module model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(){
        return $this->belongsTo(Module::class);
    }

    /**
     * This belongs to the lesson start model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lessonStart(){
        return $this->belongsTo(LessonStart::class,'lessonStart_id');
    }

}
