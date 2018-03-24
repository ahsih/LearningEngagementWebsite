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
}
