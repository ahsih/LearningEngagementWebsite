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
}
