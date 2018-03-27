<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    /**
     * This belong to the user class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users(){
        return $this->belongsTo(User::class,'user_id');
    }
}
