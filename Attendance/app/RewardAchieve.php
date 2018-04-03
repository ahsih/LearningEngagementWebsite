<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class RewardAchieve extends Model
{
    /**
     * This belong to user class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * This belong to the module
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(){
        return $this->belongsTo(Module::class);
    }
}
