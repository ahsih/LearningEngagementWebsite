<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    /**
     * This belong to the module model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(){
        return $this->belongsTo(Module::class);
    }
}
