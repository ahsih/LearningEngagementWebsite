<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class EmailRequestModule extends Model
{
    /**
     * This belong to the module class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(){
        return $this->belongsTo(Module::class);
    }
}
