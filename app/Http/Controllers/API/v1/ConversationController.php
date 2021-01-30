<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\ApiController;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConversationController extends ApiController
{
    public function list(Request $request)
    {
        $list = ChatRoom::join('chat_room_users','chat_room_users.chat_room_id','=','chat_rooms.id')
            ->where('chat_room_users.user_id',user()->id)
            ->select(
                'chat_rooms.*'
            )
            ->distinct('chat_rooms.id')
            ->get();

        foreach ($list as $key => $item) {
            $item->unread_count = Message::where('chat_room_id',$item->id)
                ->whereNull('read_at')
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
        $now = Carbon::now();
        Message::where('chat_room_id',$chat_room_id)
            ->where('sender_id','<>',user()->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => $now
            ]);
            
        $all_message = Message::where('chat_room_id',$chat_room_id)
            ->orderBy('created_at','desc')
            ->with([
                'message_reply:message_id,reply_id',
                'message_reply.message:id,message'
            ])
            ->get();
        
        $this->response->data = [
            'message'  => $all_message
        ];

        return $this->response_api();
    }
}