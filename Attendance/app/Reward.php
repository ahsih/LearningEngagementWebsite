<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    /**
     * This belong to the module class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function modules(){
        return $this->belongsTo(Module::class);
    }

    /**
     * This belong to the user class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users(){
        return $this->belongsTo(User::class);
    }
}
