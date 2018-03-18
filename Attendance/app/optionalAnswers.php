<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class optionalAnswers extends Model
{
    /**
     * This belong to the question class.
     */
    public function question(){
      return  $this->belongsTo(question::class);
    }

    /**
     * This has many response
     */
    public function response(){
       return $this->hasMany(Response::class);
    }
}
