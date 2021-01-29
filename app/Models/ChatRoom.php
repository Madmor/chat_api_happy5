<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $guarded = [];

    public function chat_room_user()
    {
        return $this->hasMany('App\Models\ChatRoomUser');
    }
}
