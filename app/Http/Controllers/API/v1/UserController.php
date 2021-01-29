<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\v1\ApiController;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends ApiController
{
    public function list(Request $request)
    {
        $user = User::all();
        
        $this->response->data = [
            'user' => $user
        ];
        $this->response->message = "Berhasil menampilkan teman!";

        return $this->response_api();
    }
}