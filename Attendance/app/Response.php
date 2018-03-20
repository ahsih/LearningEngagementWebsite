<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{

    /**
     * This belong to the question model
     */
    public function question()
    {
        return $this->belongsTo(question::class);
    }

    /**
     * This belong to the optional answer
     */
    public function optionalAnswers(){
        return $this->belongsTo(optionalAnswers::class,'optionalAnswer_id');
    }
}
