<?php

namespace attendance;

use Illuminate\Database\Eloquent\Model;

class LoginTime extends Model
{

    protected $primaryKey = 'user_id';

    /**
     * This belong to the user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
