<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    /**
     * This belong to the module
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module(){
        return $this->belongsTo(Module::class);
    }

    /**
     * This belong to the the user model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * This belong to the reward model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reward(){
        return $this->belongsTo(Reward::class);
    }
}
