<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class optionalAnswers extends Model
{
    /**
     * This belong to the question class.
     */
    public function question(){
        $this->belongsTo(question::class);
    }
}
