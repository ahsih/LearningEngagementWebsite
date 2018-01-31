<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class FirstChoiceUserModule extends Model
{
    //The table name
    protected $table= 'firstChoiceUserModule';

    /**
     * This table has one user
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * This table has one module
     */
    public function module(){
        return $this->belongsTo(Module::class);
    }
}
