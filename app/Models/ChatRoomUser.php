<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoomUser extends Model
{
    protected $guarded = [];

    public function chat_room()
    {
        return $this->belongsTo('App\Models\ChatRoom');
    }
}
