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
        return $this->belongsTo(Module::class,'module_id');
    }

    /**
     * This belong to the user class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users(){
        return $this->belongsTo(User::class);
    }

    /**
     * This has many award model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function awards(){
        return $this->hasMany(Award::class);
    }
}
