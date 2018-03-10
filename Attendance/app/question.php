<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class question extends Model
{
    //
    public function optionalAnswers(){
        $this->hasMany(optionalAnswers::class);
    }
}
