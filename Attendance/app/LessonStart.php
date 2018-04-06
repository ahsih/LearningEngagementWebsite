<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class LessonStart extends Model
{
    /**
     * This belong to the module
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(){
        return $this->belongsTo(Module::class);
    }
}
