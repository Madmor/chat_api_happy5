<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    const STATUS_READ   = 1;
    const STATUS_UNREAD = 0;
    
    protected $guarded = [];
}
