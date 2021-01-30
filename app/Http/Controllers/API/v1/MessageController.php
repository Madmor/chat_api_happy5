<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\ApiController;
use App\Models\ChatRoom;
use App\Models\ChatRoomUser;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessageController extends ApiController
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'to_email'  => 'required',
                'message'   => 'required'
            ]
        );

        if ($validator->fails()) {
            $errors = err_validator($validator->errors()->getMessages());

            $this->code = 422;
            $this->response->success = false;
            $this->response->error = $errors;
            $this->response->message = __('validation.failed');
            return $this->response_api();
        }
        DB::beginTransaction();
        $sender = user();
        $receiver = User::where('email',$request->to_email)->first();

        if(!$receiver){
            $this->code = 404;
            $this->response->success = false;
            $this->response->message = 'Alamat Email Penerima Tidak Ditemukan!';
        } else {
            $chat_room = ChatRoom::where('name',$sender->email.' - '.$receiver->email)
                ->orWhere('name',$receiver->email.' - '.$sender->email)
                ->first();
            if(!$chat_room){
                $chat_room = ChatRoom::firstOrCreate([
                    'name' => $sender->email.' - '.$receiver->email
                ]);
            }

            $chat_room_users1 = ChatRoomUser::firstOrCreate(
                [
                    'user_id' => $sender->id,
                    'chat_room_id' => $chat_room->id
                ]
            );
            $chat_room_users2 = ChatRoomUser::firstOrCreate(
                [
                    'user_id' => $receiver->id,
                    'chat_room_id' => $chat_room->id
                ]
            );

            $new_message = Message::create([
                'message'   => $request->message,
                'sender_id' => $sender->id,
                'chat_room_id' => $chat_room->id
            ]);

            $this->response->message = 'Pesan berhasil dikirim!';
        }
        DB::commit();
        return $this->response_api();
    }

    public function reply(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'message_id'=> 'required',
                'message'   => 'required'
            ]
        );

        if ($validator->fails()) {
            $errors = err_validator($validator->errors()->getMessages());

            $this->code = 422;
            $this->response->success = false;
            $this->response->error = $errors;
            $this->response->message = __('validation.failed');
            return $this->response_api();
        }

        $message    = Message::find($request->message_id);
        if(!$message){
            $this->code = 404;
            $this->response->success = false;
            $this->response->message = 'Pesan tidak ditemukan!';
            return $this->response_api();
        }
        DB::beginTransaction();
        $chat_room = ChatRoom::find($message->chat_room_id);

        $reply = Message::create([
            'message'   => $request->message,
            'sender_id' => user()->id,
            'chat_room_id' => $chat_room->id,
            'is_reply'  => true,
        ]);

        $message_reply = MessageReply::create([
            'message_id' => $message->id,
            'reply_id' => $reply->id
        ]);
        DB::commit();

        $this->response->message = 'Berhasil membalas pesan!';
        return $this->response_api();
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'message_id'=> 'required',
                'message'   => 'required'
            ]
        );

        if ($validator->fails()) {
            $errors = err_validator($validator->errors()->getMessages());

            $this->code = 422;
            $this->response->success = false;
            $this->response->error = $errors;
            $this->response->message = __('validation.failed');
            return $this->response_api();
        }

        // Validasi pesan
        $message    = Message::find($request->message_id);
        if(!$message){
            $this->code = 404;
            $this->response->success = false;
            $this->response->message = 'Pesan tidak ditemukan!';
            return $this->response_api();
        } else if($message->sender_id != user()->id){
            $this->code = 401;
            $this->response->success = false;
            $this->response->message = 'Tidak dapat mengubah pesan milik orang lain!';
            return $this->response_api();
        } else if($message->read_at != null){
            $this->code = 401;
            $this->response->success = false;
            $this->response->message = 'Tidak dapat mengubah pesan yang sudah dibaca!';
            return $this->response_api();
        }

        DB::beginTransaction();
        $now    = Carbon::now();
        $diff   = Carbon::parse($message->created_at)->diffInHours($now);
        if($diff <= 2){
            $message->message = $request->message;
            $message->save();

            $this->response->message = 'Pesan berhasil diubah!';
        } else {
            $this->response->success = false;
            $this->response->message = 'Pesan yang sudah dikirim lebih dari 2 jam tidak dapat di-edit !';
        }
        DB::commit();

        return $this->response_api();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'message_id'=> 'required',
            ]
        );

        if ($validator->fails()) {
            $errors = err_validator($validator->errors()->getMessages());

            $this->code = 422;
            $this->response->success = false;
            $this->response->error = $errors;
            $this->response->message = __('validation.failed');
            return $this->response_api();
        }

        $message    = Message::find($request->message_id);
        if(!$message){
            $this->code = 404;
            $this->response->success = false;
            $this->response->message = 'Pesan tidak ditemukan!';
            return $this->response_api();
        } else if($message->sender_id != user()->id){
            $this->code = 401;
            $this->response->success = false;
            $this->response->message = 'Tidak dapat menghapus pesan milik orang lain!';
            return $this->response_api();
        } else if($message->status != null){
            $this->code = 401;
            $this->response->success = false;
            $this->response->message = 'Tidak dapat menghapus pesan yang sudah dibaca!';
            return $this->response_api();
        }

        DB::beginTransaction();
        $now    = Carbon::now();
        $diff   = Carbon::parse($message->created_at)->diffInHours($now);
        if($diff <= 2){
            $message->delete();

            $this->response->message = 'Pesan berhasil dihapus!';
        } else {
            $this->response->success = false;
            $this->response->message = 'Pesan yang sudah dikirim lebih dari 2 jam tidak dapat di-hapus !';
        }
        DB::commit();
        return $this->response_api();
    }
}