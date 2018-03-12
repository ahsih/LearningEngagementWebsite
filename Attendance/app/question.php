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
     * This belong to the module
     */
    public function modules()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * This model belong to the user
     */
    public function users()
    {
        return $this->belongsTo(User::class);
    }

}
