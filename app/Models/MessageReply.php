<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageReply extends Model
{
    protected $guarded = [];

    public function message()
    {
        return $this->belongsTo('App\Models\Message','message_id');
    }
}