<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\ApiController;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConversationController extends ApiController
{
    public function list(Request $request)
    {
        $list = ChatRoom::join('chat_room_users','chat_room_users.chat_room_id','=','chat_rooms.id')
            ->join('messages','messages.chat_room_id','=','chat_rooms.id')
            ->where('chat_room_users.user_id',user()->id)
            ->select(
                'chat_rooms.*'
            )
            ->distinct('chat_rooms.id')
            ->get();

        foreach ($list as $key => $item) {
            $item->unread_count = Message::where('chat_room_id',$item->id)
                ->where('status',Message::STATUS_UNREAD)
                ->where('sender_id','<>',user()->id)
                ->count();
            
            $item->last_message = Message::where('chat_room_id',$item->id)
                ->orderBy('created_at','desc')
                ->first()
                ->message;
        }

        $this->response->message = "Berhasil mengambil data percakapan!";
        $this->response->data = [
            'conversation' => $list
        ];
        
        return $this->response_api();
    }

    public function detail(Request $request, $chat_room_id)
    {
        Message::where('chat_room_id',$chat_room_id)
            ->where('sender_id','<>',user()->id)
            ->update([
                'status' => Message::STATUS_READ
            ]);
            
        $all_message = Message::where('chat_room_id',$chat_room_id)
            ->orderBy('created_at','desc')
            ->get();
        
        $this->response->data = [
            'message'  => $all_message
        ];

        return $this->response_api();
    }
}